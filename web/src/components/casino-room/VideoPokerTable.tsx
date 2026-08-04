import { AnimatePresence, motion, useReducedMotion } from 'motion/react';
import type { CSSProperties } from 'react';
import { useRef, useState } from 'react';
import {
  evaluateJacksOrBetter,
  shuffledVideoPokerDeck,
  VIDEO_POKER_CREDIT_VALUE,
  VIDEO_POKER_PAYTABLE,
  videoPokerCardImage,
  videoPokerCardLabel,
  videoPokerPayoutCredits,
  type VideoPokerCard,
} from '@/lib/video-poker';
import { completeTrainingRound } from '@/lib/training-wallet';
import type { GameProps } from './types';

type Phase = 'betting' | 'dealing' | 'holding' | 'drawing' | 'complete';
type TableCard = VideoPokerCard & { id: string };

const CREDIT_OPTIONS = [1, 2, 3, 4, 5];
const sleep = (milliseconds: number) => new Promise((resolve) => window.setTimeout(resolve, milliseconds));
const numberFormat = new Intl.NumberFormat('en-US');

function PokerCard({ card, index, held, interactive, onToggle }: {
  card: TableCard;
  index: number;
  held: boolean;
  interactive: boolean;
  onToggle: () => void;
}) {
  const reduceMotion = useReducedMotion();
  return (
    <motion.button
      type="button"
      className="vp-card"
      data-held={held}
      disabled={!interactive}
      aria-pressed={held}
      aria-label={`${videoPokerCardLabel(card)}${held ? ', held' : ', tap to hold'}`}
      onClick={onToggle}
      style={{ '--vp-slot-index': index } as CSSProperties}
      initial={reduceMotion ? false : { opacity: .94, x: 'var(--vp-deal-x)', y: 'var(--vp-deal-y)', rotate: 11 - index * 1.5, scale: .6 }}
      animate={reduceMotion ? { opacity: 1 } : { opacity: [1, 1, 1], x: ['var(--vp-deal-x)', 'var(--vp-arc-x)', 0], y: ['var(--vp-deal-y)', 'var(--vp-arc-y)', 0], rotate: [11 - index * 1.5, 4, 0], scale: [.6, .86, 1] }}
      exit={reduceMotion ? undefined : { opacity: 0, x: -34, y: 76, rotate: -10, scale: .72 }}
      transition={{ duration: reduceMotion ? .01 : .78, times: [0, .56, 1], ease: [0.22, 1, 0.36, 1] }}
    >
      <motion.div className="vp-card-lift" animate={{ y: held ? -10 : 0 }} transition={{ type: 'spring', stiffness: 300, damping: 24 }}>
        <motion.div className="vp-card-flipper" initial={{ rotateY: 0 }} animate={{ rotateY: 180 }} transition={{ duration: reduceMotion ? .01 : .5, delay: reduceMotion ? 0 : .36, ease: [0.22, 1, 0.36, 1] }}>
          <div className="vp-card-face vp-card-back"><img src="/cards/back-cards.png" alt="" width="292" height="424" draggable="false" /></div>
          <div className="vp-card-face vp-card-front"><img src={videoPokerCardImage(card)} alt="" width="292" height="424" draggable="false" /></div>
        </motion.div>
        <span>{held ? 'Held' : 'Hold'}</span>
      </motion.div>
    </motion.button>
  );
}

export default function VideoPokerTable({ wallet, refreshWallet, onBusyChange }: GameProps) {
  const reduceMotion = useReducedMotion();
  const [phase, setPhase] = useState<Phase>('betting');
  const [credits, setCredits] = useState(5);
  const [hand, setHand] = useState<TableCard[]>([]);
  const [held, setHeld] = useState<boolean[]>(Array(5).fill(false));
  const [status, setStatus] = useState('Choose 1–5 credits, then deal. One credit equals 10 chips.');
  const [tone, setTone] = useState<'neutral' | 'success' | 'error'>('neutral');
  const [lastWin, setLastWin] = useState<number | null>(null);
  const deckRef = useRef<VideoPokerCard[]>([]);
  const sequence = useRef(0);
  const wager = credits * VIDEO_POKER_CREDIT_VALUE;
  const wait = (milliseconds: number) => sleep(reduceMotion ? 20 : milliseconds);

  const drawCard = (): TableCard => {
    const card = deckRef.current.pop();
    if (!card) throw new Error('Video poker deck exhausted');
    return { ...card, id: `vp-${Date.now()}-${sequence.current += 1}` };
  };

  const deal = async (requestedCredits = credits) => {
    const nextCredits = Math.min(5, Math.max(1, requestedCredits));
    const nextWager = nextCredits * VIDEO_POKER_CREDIT_VALUE;
    if (phase === 'dealing' || phase === 'drawing') return;
    if (wallet.balance < nextWager) {
      setTone('error');
      setStatus(`You need ${numberFormat.format(nextWager)} chips to play ${nextCredits} credits.`);
      return;
    }

    setCredits(nextCredits);
    setPhase('dealing');
    setTone('neutral');
    setLastWin(null);
    setHand([]);
    setHeld(Array(5).fill(false));
    setStatus('Dealing five cards.');
    deckRef.current = shuffledVideoPokerDeck();
    onBusyChange(true);

    const nextHand: TableCard[] = [];
    for (let index = 0; index < 5; index += 1) {
      nextHand.push(drawCard());
      setHand([...nextHand]);
      await wait(470);
    }
    setPhase('holding');
    setStatus('Tap the cards you want to hold, then draw.');
    onBusyChange(false);
  };

  const toggleHold = (index: number) => {
    if (phase !== 'holding') return;
    setHeld((current) => current.map((value, cardIndex) => cardIndex === index ? !value : value));
  };

  const draw = async () => {
    if (phase !== 'holding') return;
    setPhase('drawing');
    setStatus('Drawing replacement cards.');
    onBusyChange(true);
    const nextHand = [...hand];
    for (let index = 0; index < 5; index += 1) {
      if (held[index]) continue;
      nextHand[index] = drawCard();
      setHand([...nextHand]);
      await wait(900);
    }

    const result = evaluateJacksOrBetter(nextHand);
    const payoutCredits = videoPokerPayoutCredits(result, credits);
    const payout = payoutCredits * VIDEO_POKER_CREDIT_VALUE;
    const completion = completeTrainingRound({ game: 'video-poker', result: result.label, wagered: wager, payout });
    setLastWin(payout);
    setPhase('complete');
    setTone(payout > wager ? 'success' : payout === wager ? 'neutral' : 'error');
    setStatus(payout > 0 ? `${result.label}. ${numberFormat.format(payout)} chips returned.` : 'No winning hand. Deal again when ready.');
    onBusyChange(false);
    refreshWallet(completion.wallet);
  };

  const interactive = phase === 'holding';

  return (
    <div className="vp-game" data-video-poker data-phase={phase}>
      <div className="vp-main">
        <aside className="vp-paytable" aria-label="Jacks or Better paytable">
          <header><span>9 / 6</span><div><small>Game</small><strong>Jacks or Better</strong></div></header>
          <div className="vp-paytable-grid" role="table">
            <div className="vp-paytable-head" role="row"><span role="columnheader">Hand</span>{CREDIT_OPTIONS.map((credit) => <b key={credit} role="columnheader" data-max={credit === 5}>{credit}</b>)}</div>
            {VIDEO_POKER_PAYTABLE.map((row) => <div className="vp-paytable-row" role="row" key={row.key} data-royal={row.key === 'royal-flush'}>
              <span role="cell">{row.label}</span>
              {CREDIT_OPTIONS.map((credit) => <b key={credit} role="cell" data-max={credit === 5}>{row.key === 'royal-flush' && credit === 5 ? '4000' : row.multiplier * credit}</b>)}
            </div>)}
          </div>
        </aside>

        <section className="vp-table" aria-label="Video poker hand">
          <div className="vp-machine-title"><span>Draw poker</span><strong>Jacks or Better</strong><small>Pair of jacks or higher to win</small></div>
          <div className="vp-deck" aria-hidden="true">{[0, 1, 2, 3].map((card) => <img key={card} src="/cards/back-cards.png" alt="" data-video-poker-deck-source={card === 3 ? 'true' : undefined} style={{ '--vp-deck-card': card } as CSSProperties} />)}</div>
          <div className="vp-hand">
            {CREDIT_OPTIONS.map((slot, index) => (
              <div className="vp-card-slot" data-empty={!hand[index]} key={slot}>
                <AnimatePresence mode="wait" initial={false}>
                  {hand[index] && <PokerCard key={hand[index].id} card={hand[index]} index={index} held={held[index]} interactive={interactive} onToggle={() => toggleHold(index)} />}
                </AnimatePresence>
              </div>
            ))}
          </div>
          <motion.div className="vp-win-display" data-visible={phase === 'complete'} data-tone={tone} animate={{ scale: phase === 'complete' ? 1 : .92 }}>
            <span>Win</span><strong>{numberFormat.format(lastWin ?? 0)}</strong><small>chips</small>
          </motion.div>
        </section>
      </div>

      <div className="vp-console">
        <div className="vp-status" data-tone={tone} aria-live="polite"><i />{status}</div>
        <div className="vp-credit-select" aria-label="Choose number of credits">
          <span>Credits</span>
          <div>{CREDIT_OPTIONS.map((value) => <button type="button" key={value} className={credits === value ? 'is-selected' : ''} disabled={phase === 'dealing' || phase === 'drawing' || phase === 'holding'} aria-pressed={credits === value} onClick={() => setCredits(value)}>{value}</button>)}</div>
        </div>
        <div className="vp-wager"><span>Bet</span><strong>{numberFormat.format(wager)} chips</strong></div>
        <div className="vp-actions">
          <button type="button" className="vp-max" disabled={phase === 'dealing' || phase === 'drawing' || phase === 'holding'} onClick={() => deal(5)}>Max bet</button>
          {phase === 'holding'
            ? <motion.button whileTap={{ scale: .96 }} type="button" className="vp-primary" data-draw onClick={draw}>Draw</motion.button>
            : <motion.button whileTap={{ scale: .96 }} type="button" className="vp-primary" data-deal disabled={phase === 'dealing' || phase === 'drawing'} onClick={() => deal(credits)}>Deal</motion.button>}
        </div>
      </div>
    </div>
  );
}
