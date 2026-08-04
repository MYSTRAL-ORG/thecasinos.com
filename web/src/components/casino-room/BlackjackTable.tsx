import { motion, useReducedMotion } from 'motion/react';
import { useMemo, useRef, useState, type CSSProperties } from 'react';
import {
  blackjackCardLabel,
  blackjackCardPath,
  blackjackHandValue,
  canSplitBlackjackHand,
  dealerShouldHit,
  recommendedBlackjackAction,
  settleBlackjackHand,
  shuffledBlackjackShoe,
  type BlackjackAction,
  type BlackjackCard,
} from '@/lib/blackjack';
import { completeTrainingRound } from '@/lib/training-wallet';
import type { GameProps } from './types';

type Phase = 'betting' | 'dealing' | 'player' | 'dealer' | 'complete';
type TableCard = BlackjackCard & { id: string };
type PlayerHand = {
  id: string;
  cards: TableCard[];
  stake: number;
  done: boolean;
  fromSplit: boolean;
  result?: string;
};

const STAKES = [10, 20, 50, 100, 500];
const actionLabels: Record<BlackjackAction, string> = { hit: 'Hit', stand: 'Stand', double: 'Double', split: 'Split' };
const numberFormat = new Intl.NumberFormat('en-US');
const sleep = (milliseconds: number) => new Promise((resolve) => window.setTimeout(resolve, milliseconds));

function TablePlayingCard({ card, faceDown = false, order = 0 }: { card: TableCard; faceDown?: boolean; order?: number }) {
  const reduceMotion = useReducedMotion();
  return (
    <motion.div
      layout
      layoutId={card.id}
      className="bj-card-wrap"
      initial={reduceMotion ? false : { opacity: 0, x: 128, y: -82, rotate: 12, scale: .72 }}
      animate={{ opacity: 1, x: 0, y: 0, rotate: 0, scale: 1 }}
      transition={{ type: 'spring', stiffness: 245, damping: 24, delay: reduceMotion ? 0 : order * .035 }}
    >
      <motion.div
        className="bj-card-flipper"
        animate={{ rotateY: faceDown ? 0 : 180 }}
        transition={{ duration: reduceMotion ? .01 : .52, ease: [0.22, 1, 0.36, 1] }}
      >
        <div className="bj-card-face bj-card-back"><img src="/cards/back-cards.png" alt="Face-down card" width="292" height="424" draggable={false} /></div>
        <div className="bj-card-face bj-card-front"><img src={blackjackCardPath(card)} alt={blackjackCardLabel(card)} width="292" height="424" draggable={false} /></div>
      </motion.div>
    </motion.div>
  );
}

function CardShoe() {
  return (
    <div className="bj-shoe" aria-label="Dealer shoe">
      {[0, 1, 2, 3].map((card) => <img key={card} src="/cards/back-cards.png" alt="" width="292" height="424" style={{ '--shoe-card': card } as CSSProperties} />)}
      <span>Shoe</span>
    </div>
  );
}

export default function BlackjackTable({ wallet, refreshWallet, onBusyChange }: GameProps) {
  const reduceMotion = useReducedMotion();
  const [phase, setPhase] = useState<Phase>('betting');
  const [stake, setStake] = useState(20);
  const [dealerCards, setDealerCards] = useState<TableCard[]>([]);
  const [hands, setHands] = useState<PlayerHand[]>([]);
  const [activeHandIndex, setActiveHandIndex] = useState(0);
  const [dealerRevealed, setDealerRevealed] = useState(false);
  const [status, setStatus] = useState('Choose a chip, place your bet and deal.');
  const [tone, setTone] = useState<'neutral' | 'success' | 'error'>('neutral');
  const shoe = useRef<TableCard[]>([]);
  const cardSequence = useRef(0);
  const handSequence = useRef(0);
  const locked = useRef(false);

  const wait = (milliseconds: number) => sleep(reduceMotion ? 20 : milliseconds);
  const setBusy = (busy: boolean) => { locked.current = busy; onBusyChange(busy); };
  const totalStake = hands.reduce((sum, hand) => sum + hand.stake, 0);
  const activeHand = hands[activeHandIndex];

  const drawCard = () => {
    if (shoe.current.length < 70) {
      shoe.current = shuffledBlackjackShoe().map((card) => ({ ...card, id: `bj-card-${cardSequence.current += 1}` }));
    }
    const card = shoe.current.pop();
    if (!card) throw new Error('The blackjack shoe is empty.');
    return card;
  };

  const finishRound = async (currentDealer: TableCard[], currentHands: PlayerHand[]) => {
    const wagered = currentHands.reduce((sum, hand) => sum + hand.stake, 0);
    let payout = 0;
    const settledHands = currentHands.map((hand) => {
      const settlement = settleBlackjackHand(hand.cards, currentDealer, hand.stake, !hand.fromSplit);
      payout += settlement.payout;
      return { ...hand, done: true, result: settlement.result };
    });
    const result = settledHands.map((hand, index) => `${settledHands.length > 1 ? `H${index + 1} ` : ''}${hand.result}`).join(' · ');
    const completion = completeTrainingRound({ game: 'blackjack', result, wagered, payout });
    setHands(settledHands);
    setPhase('complete');
    setTone(payout > wagered ? 'success' : payout === wagered ? 'neutral' : 'error');
    setStatus(payout > wagered
      ? `${result}. ${numberFormat.format(payout)} chips returned.`
      : payout === wagered ? `${result}. Your stake is returned.` : `${result}. The dealer takes the bet.`);
    refreshWallet(completion.wallet);
    setBusy(false);
  };

  const playDealer = async (initialDealer: TableCard[], currentHands: PlayerHand[]) => {
    setPhase('dealer');
    setDealerRevealed(true);
    setStatus('Dealer reveals the hole card.');
    await wait(620);
    const nextDealer = [...initialDealer];
    const allPlayersBust = currentHands.every((hand) => blackjackHandValue(hand.cards).bust);
    while (!allPlayersBust && dealerShouldHit(nextDealer)) {
      nextDealer.push(drawCard());
      setDealerCards([...nextDealer]);
      setStatus(`Dealer draws to ${blackjackHandValue(nextDealer).total}.`);
      await wait(520);
    }
    await wait(220);
    await finishRound(nextDealer, currentHands);
  };

  const deal = async () => {
    if (locked.current || stake > wallet.balance) {
      setTone('error');
      setStatus('Choose a bet covered by your current balance.');
      return;
    }
    setBusy(true);
    setPhase('dealing');
    setTone('neutral');
    setStatus('Dealing from the shoe…');
    setDealerRevealed(false);
    setDealerCards([]);
    setHands([]);
    setActiveHandIndex(0);

    const hand: PlayerHand = { id: `bj-hand-${handSequence.current += 1}`, cards: [], stake, done: false, fromSplit: false };
    const dealer: TableCard[] = [];
    setHands([hand]);
    await wait(120);
    hand.cards = [drawCard()]; setHands([{ ...hand, cards: [...hand.cards] }]);
    await wait(260);
    dealer.push(drawCard()); setDealerCards([...dealer]);
    await wait(260);
    hand.cards.push(drawCard()); setHands([{ ...hand, cards: [...hand.cards] }]);
    await wait(260);
    dealer.push(drawCard()); setDealerCards([...dealer]);
    await wait(360);

    const playerNatural = blackjackHandValue(hand.cards).blackjack;
    const dealerNatural = blackjackHandValue(dealer).blackjack;
    if (playerNatural || dealerNatural) {
      await playDealer(dealer, [{ ...hand, cards: [...hand.cards], done: true }]);
      return;
    }
    setPhase('player');
    setStatus('Your move. The highlighted action is the basic-strategy play.');
    setBusy(false);
  };

  const advanceHands = async (nextHands: PlayerHand[], currentIndex: number, currentDealer = dealerCards) => {
    const nextIndex = nextHands.findIndex((hand, index) => index > currentIndex && !hand.done);
    setHands(nextHands);
    if (nextIndex >= 0) {
      setActiveHandIndex(nextIndex);
      setPhase('player');
      setStatus(`Hand ${nextIndex + 1}: choose the next action.`);
      setBusy(false);
    } else {
      await playDealer(currentDealer, nextHands);
    }
  };

  const act = async (action: BlackjackAction) => {
    if (phase !== 'player' || locked.current || !activeHand) return;
    setBusy(true);
    setTone('neutral');
    const nextHands = hands.map((hand) => ({ ...hand, cards: [...hand.cards] }));
    const hand = nextHands[activeHandIndex];

    if (action === 'hit') {
      hand.cards.push(drawCard());
      setHands(nextHands);
      setStatus('Card dealt.');
      await wait(460);
      const value = blackjackHandValue(hand.cards);
      if (value.bust || value.total === 21) {
        hand.done = true;
        await advanceHands(nextHands, activeHandIndex);
      } else {
        setBusy(false);
      }
      return;
    }

    if (action === 'stand') {
      hand.done = true;
      setStatus(`Standing on ${blackjackHandValue(hand.cards).total}.`);
      await advanceHands(nextHands, activeHandIndex);
      return;
    }

    if (action === 'double') {
      if (hand.cards.length !== 2 || totalStake + hand.stake > wallet.balance) {
        setBusy(false); return;
      }
      hand.stake *= 2;
      hand.cards.push(drawCard());
      hand.done = true;
      setHands(nextHands);
      setStatus('Bet doubled. One final card.');
      await wait(520);
      await advanceHands(nextHands, activeHandIndex);
      return;
    }

    if (action === 'split') {
      if (hands.length !== 1 || !canSplitBlackjackHand(hand.cards) || totalStake + hand.stake > wallet.balance) {
        setBusy(false); return;
      }
      const [first, second] = hand.cards;
      const splitHands: PlayerHand[] = [
        { id: hand.id, cards: [first, drawCard()], stake: hand.stake, done: false, fromSplit: true },
        { id: `bj-hand-${handSequence.current += 1}`, cards: [second, drawCard()], stake: hand.stake, done: false, fromSplit: true },
      ];
      setHands(splitHands);
      setActiveHandIndex(0);
      setStatus('Pair split into two hands. Play hand 1 first.');
      await wait(560);
      setBusy(false);
    }
  };

  const availability = useMemo(() => {
    const hand = hands[activeHandIndex];
    const playing = phase === 'player' && Boolean(hand) && !hand.done;
    const extraCovered = playing && totalStake + (hand?.stake ?? 0) <= wallet.balance;
    return {
      playing,
      canDouble: Boolean(extraCovered && hand?.cards.length === 2),
      canSplit: Boolean(extraCovered && hands.length === 1 && hand && canSplitBlackjackHand(hand.cards)),
    };
  }, [activeHandIndex, hands, phase, totalStake, wallet.balance]);

  const recommendation = phase === 'player' && activeHand && dealerCards[0]
    ? recommendedBlackjackAction(activeHand.cards, dealerCards[0], availability)
    : null;
  const playerScore = activeHand ? blackjackHandValue(activeHand.cards).total : null;
  const dealerScore = dealerCards.length ? blackjackHandValue(dealerRevealed ? dealerCards : dealerCards.slice(0, 1)).total : null;
  const showBetting = phase === 'betting' || phase === 'complete';

  return (
    <div className="bj-game" data-blackjack-game data-phase={phase}>
      <div className="bj-table">
        <div className="bj-table-arc" aria-hidden="true"><span>Blackjack pays 3:2</span><i /><span>Dealer stands on 17</span></div>
        <CardShoe />

        <section className="bj-seat bj-dealer-seat" aria-label="Dealer hand">
          <header><span>Dealer</span><strong>{dealerScore ?? '–'}</strong><small>stands on 17</small></header>
          <div className="bj-hand-row">
            {dealerCards.length ? dealerCards.map((card, index) => <TablePlayingCard key={card.id} card={card} faceDown={!dealerRevealed && index === 1} order={index} />) : <span className="bj-empty">Cards will arrive from the shoe</span>}
          </div>
        </section>

        <div className="bj-bet-marker" data-live={!showBetting && hands.length > 0}>
          <span>{numberFormat.format(showBetting ? stake : totalStake)}</span><small>Your bet</small>
        </div>

        <section className="bj-seat bj-player-seat" aria-label="Player hand">
          <header><span>{hands.length > 1 ? `Hand ${activeHandIndex + 1}` : 'Your hand'}</span><strong>{playerScore ?? '–'}</strong><small>{activeHand ? `${numberFormat.format(activeHand.stake)} chips` : 'place a bet to begin'}</small></header>
          <div className="bj-player-hands">
            {hands.length ? hands.map((hand, handIndex) => (
              <motion.div layout key={hand.id} className="bj-hand-row" data-active={phase === 'player' && handIndex === activeHandIndex}>
                {hand.cards.map((card, cardIndex) => <TablePlayingCard key={card.id} card={card} order={cardIndex} />)}
                {hand.result && <motion.span className={`bj-hand-result bj-result-${hand.result}`} initial={{ opacity: 0, scale: .8 }} animate={{ opacity: 1, scale: 1 }}>{hand.result}</motion.span>}
              </motion.div>
            )) : <div className="bj-hand-row"><span className="bj-empty">Your cards appear here</span></div>}
          </div>
        </section>
      </div>

      <div className="bj-console">
        <div className="bj-status-line" data-tone={tone} aria-live="polite"><span className="bj-status-dot" />{status}</div>
        <div className="bj-coach">
          <span>Strategy coach</span>
          <strong>{recommendation ? `${actionLabels[recommendation]} is the play` : phase === 'complete' ? 'Hand complete' : phase === 'dealer' ? 'Dealer is playing' : 'Ready when you are'}</strong>
          <small>{recommendation && activeHand && dealerCards[0] ? `Your ${blackjackHandValue(activeHand.cards).total} against dealer ${blackjackHandValue([dealerCards[0]]).total}.` : 'Standard multi-deck strategy · dealer stands on soft 17.'}</small>
        </div>

        {showBetting ? <div className="bj-betting-controls">
          <div className="bj-chip-rack" aria-label="Choose your blackjack bet">
            {STAKES.map((value) => <motion.button whileTap={{ scale: .9 }} key={value} type="button" data-blackjack-stake={value} className={stake === value ? 'is-selected' : ''} disabled={value > wallet.balance} aria-pressed={stake === value} onClick={() => setStake(value)}>{value}</motion.button>)}
          </div>
          <motion.button whileTap={{ scale: .97 }} className="bj-deal" data-blackjack-deal type="button" disabled={stake > wallet.balance} onClick={deal}>
            <span>{phase === 'complete' ? 'Next hand' : 'Place bet'}</span><strong>Bet {numberFormat.format(stake)} &amp; deal</strong>
          </motion.button>
        </div> : <div className="bj-action-controls">
          {(['hit', 'stand', 'double', 'split'] as BlackjackAction[]).map((action) => (
            <motion.button whileTap={{ scale: .96 }} key={action} type="button" data-blackjack-action={action} className={recommendation === action ? 'is-recommended' : ''} disabled={!availability.playing || (action === 'double' && !availability.canDouble) || (action === 'split' && !availability.canSplit)} onClick={() => act(action)}>
              {actionLabels[action]}{recommendation === action && <small>Coach</small>}
            </motion.button>
          ))}
        </div>}
      </div>
    </div>
  );
}
