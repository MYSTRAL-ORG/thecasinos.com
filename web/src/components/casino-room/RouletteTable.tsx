import { motion, useAnimationControls, useReducedMotion } from 'motion/react';
import { useMemo, useState } from 'react';
import {
  rouletteColor,
  rouletteRoundPayout,
  secureRouletteResult,
  type RouletteBet,
} from '@/lib/roulette';
import { completeTrainingRound } from '@/lib/training-wallet';
import type { GameProps } from './types';

const EUROPEAN_SEQUENCE = [0, 32, 15, 19, 4, 21, 2, 25, 17, 34, 6, 27, 13, 36, 11, 30, 8, 23, 10, 5, 24, 16, 33, 1, 20, 14, 31, 9, 22, 18, 29, 7, 28, 12, 35, 3, 26];
const STAKES = [1, 5, 10, 25, 100];
const numberFormat = new Intl.NumberFormat('en-US');

function polar(radius: number, angle: number) {
  const radians = (angle - 90) * Math.PI / 180;
  return {
    x: Number((160 + radius * Math.cos(radians)).toFixed(5)),
    y: Number((160 + radius * Math.sin(radians)).toFixed(5)),
  };
}

function ringSegment(startAngle: number, endAngle: number, innerRadius = 104, outerRadius = 145) {
  const outerStart = polar(outerRadius, startAngle);
  const outerEnd = polar(outerRadius, endAngle);
  const innerEnd = polar(innerRadius, endAngle);
  const innerStart = polar(innerRadius, startAngle);
  return `M ${outerStart.x} ${outerStart.y} A ${outerRadius} ${outerRadius} 0 0 1 ${outerEnd.x} ${outerEnd.y} L ${innerEnd.x} ${innerEnd.y} A ${innerRadius} ${innerRadius} 0 0 0 ${innerStart.x} ${innerStart.y} Z`;
}

function RouletteWheel({ rotation, ballRotation, controls, ballControls }: {
  rotation: number;
  ballRotation: number;
  controls: ReturnType<typeof useAnimationControls>;
  ballControls: ReturnType<typeof useAnimationControls>;
}) {
  const step = 360 / EUROPEAN_SEQUENCE.length;
  return (
    <div className="rt-wheel-stage">
      <span className="rt-wheel-pointer" aria-hidden="true" />
      <motion.div className="rt-ball-orbit" initial={{ rotate: ballRotation }} animate={ballControls}><i /></motion.div>
      <svg className="rt-wheel" viewBox="0 0 320 320" role="img" aria-label="European roulette wheel with 37 numbered pockets">
        <circle cx="160" cy="160" r="157" fill="#071c17" stroke="rgba(255,255,255,.18)" strokeWidth="2" />
        <motion.g initial={{ rotate: rotation }} animate={controls} style={{ transformOrigin: '160px 160px' }}>
          {EUROPEAN_SEQUENCE.map((number, index) => {
            const start = index * step;
            const center = Number((start + step / 2).toFixed(6));
            const label = polar(124, center);
            const color = rouletteColor(number);
            return <g key={number}>
              <path d={ringSegment(start, start + step)} fill={color === 'red' ? '#d64e57' : color === 'black' ? '#17211f' : '#3f9b6b'} stroke="#071c17" strokeWidth="1.4" />
              <text x={label.x} y={label.y} fill="#fff" textAnchor="middle" dominantBaseline="middle" fontSize="8.8" fontWeight="800" transform={`rotate(${center + 90} ${label.x} ${label.y})`}>{number}</text>
            </g>;
          })}
          <circle cx="160" cy="160" r="98" fill="#123d34" stroke="#d8b66a" strokeWidth="4" />
          <circle cx="160" cy="160" r="72" fill="#08241e" stroke="rgba(255,255,255,.13)" strokeWidth="2" />
          <g fill="#d8b66a">
            {Array.from({ length: 8 }, (_, index) => <rect key={index} x="157" y="88" width="6" height="42" rx="3" transform={`rotate(${index * 45} 160 160)`} />)}
          </g>
          <circle cx="160" cy="160" r="32" fill="#d8b66a" />
          <circle cx="160" cy="160" r="20" fill="#123d34" />
        </motion.g>
      </svg>
    </div>
  );
}

export default function RouletteTable({ wallet, refreshWallet, onBusyChange }: GameProps) {
  const reduceMotion = useReducedMotion();
  const wheelControls = useAnimationControls();
  const ballControls = useAnimationControls();
  const [stake, setStake] = useState(10);
  const [bets, setBets] = useState<Map<string, RouletteBet>>(new Map());
  const [betHistory, setBetHistory] = useState<Map<string, RouletteBet>[]>([]);
  const [spinning, setSpinning] = useState(false);
  const [rotation, setRotation] = useState(0);
  const [ballRotation, setBallRotation] = useState(0);
  const [result, setResult] = useState<number | null>(null);
  const [status, setStatus] = useState('Choose a chip, then tap the layout to build your bet.');
  const [tone, setTone] = useState<'neutral' | 'success' | 'error'>('neutral');

  const totalBet = useMemo(() => [...bets.values()].reduce((sum, bet) => sum + bet.amount, 0), [bets]);
  const recent = wallet.history.filter((entry) => entry.game === 'roulette').slice(0, 6);

  const placeBet = (kind: RouletteBet['kind'], value: RouletteBet['value'], label: string) => {
    if (spinning || totalBet + stake > wallet.balance) {
      setTone('error');
      setStatus('Your current balance cannot cover that chip.');
      return;
    }
    const key = `${kind}:${String(value)}`;
    setBetHistory((history) => [...history.slice(-11), new Map(bets)]);
    setBets((current) => {
      const next = new Map(current);
      const previous = next.get(key);
      next.set(key, { key, kind, value, amount: (previous?.amount ?? 0) + stake });
      return next;
    });
    setTone('neutral');
    setStatus(`${numberFormat.format(stake)} chips placed on ${label}.`);
  };

  const undo = () => {
    const previous = betHistory.at(-1);
    if (!previous || spinning) return;
    setBets(new Map(previous));
    setBetHistory((history) => history.slice(0, -1));
    setStatus('Last chip removed.');
  };

  const clear = () => {
    if (spinning) return;
    setBets(new Map()); setBetHistory([]); setStatus('Table cleared.'); setTone('neutral');
  };

  const spin = async () => {
    if (spinning || totalBet <= 0) {
      setTone('error'); setStatus('Place at least one chip before spinning.'); return;
    }
    if (totalBet > wallet.balance) {
      setTone('error'); setStatus('Your balance no longer covers this layout.'); return;
    }
    setSpinning(true); onBusyChange(true); setTone('neutral'); setStatus('No more bets. The wheel is live.');
    const outcome = secureRouletteResult();
    const index = EUROPEAN_SEQUENCE.indexOf(outcome);
    const step = 360 / EUROPEAN_SEQUENCE.length;
    const targetModulo = (360 - (index * step + step / 2)) % 360;
    const currentModulo = ((rotation % 360) + 360) % 360;
    const delta = (targetModulo - currentModulo + 360) % 360;
    const targetRotation = rotation + (reduceMotion ? 0 : 6 * 360) + delta;
    const targetBall = ballRotation - (reduceMotion ? 0 : 8 * 360) - (((ballRotation % 360) + 360) % 360);

    await Promise.all([
      wheelControls.start({ rotate: targetRotation, transition: { duration: reduceMotion ? .05 : 3.35, ease: [0.12, .72, .12, 1] } }),
      ballControls.start({ rotate: targetBall, transition: { duration: reduceMotion ? .05 : 3.05, ease: [0.08, .75, .18, 1] } }),
    ]);

    const payout = rouletteRoundPayout(bets.values(), outcome);
    const completion = completeTrainingRound({ game: 'roulette', result: String(outcome), wagered: totalBet, payout });
    setRotation(targetRotation); setBallRotation(targetBall); setResult(outcome);
    setTone(payout > totalBet ? 'success' : payout === totalBet ? 'neutral' : 'error');
    setStatus(payout > 0 ? `${outcome} ${rouletteColor(outcome)}. ${numberFormat.format(payout)} chips returned.` : `${outcome} ${rouletteColor(outcome)}. No winning bet.`);
    setBets(new Map()); setBetHistory([]); setSpinning(false); onBusyChange(false); refreshWallet(completion.wallet);
  };

  const betAmount = (kind: RouletteBet['kind'], value: RouletteBet['value']) => bets.get(`${kind}:${String(value)}`)?.amount;
  const betButton = (kind: RouletteBet['kind'], value: RouletteBet['value'], label: string, className = '') => {
    const amount = betAmount(kind, value);
    return <motion.button key={`${kind}:${String(value)}`} whileTap={{ scale: .93 }} type="button" data-bet-key={`${kind}:${String(value)}`} className={`rt-bet ${className} ${amount ? 'has-bet' : ''}`} disabled={spinning} onClick={() => placeBet(kind, value, label)}>
      <span>{label}</span>{amount ? <i>{numberFormat.format(amount)}</i> : null}
    </motion.button>;
  };

  return (
    <div className="rt-game" data-roulette-game data-phase={spinning ? 'spinning' : 'betting'}>
      <div className="rt-main">
        <section className="rt-wheel-column">
          <RouletteWheel rotation={rotation} ballRotation={ballRotation} controls={wheelControls} ballControls={ballControls} />
          <div className="rt-result" data-color={result === null ? 'none' : rouletteColor(result)}>
            <motion.strong data-result-number key={result ?? 'empty'} initial={{ scale: .7, opacity: 0 }} animate={{ scale: 1, opacity: 1 }}>{result ?? '–'}</motion.strong>
            <span>{result === null ? 'Waiting for a spin' : rouletteColor(result)}</span>
          </div>
        </section>

        <section className="rt-layout" aria-label="Roulette betting layout">
          <div className="rt-number-board">
            {betButton('number', 0, '0', 'rt-zero rt-green')}
            <div className="rt-number-grid">
              {Array.from({ length: 36 }, (_, index) => index + 1).map((number) => betButton('number', number, String(number), `rt-number rt-${rouletteColor(number)}`))}
            </div>
          </div>
          <div className="rt-dozens">
            {betButton('dozen', 1, '1st 12')}{betButton('dozen', 2, '2nd 12')}{betButton('dozen', 3, '3rd 12')}
          </div>
          <div className="rt-outside">
            {betButton('range', 'low', '1–18')}{betButton('parity', 'even', 'Even')}{betButton('color', 'red', 'Red', 'rt-red')}{betButton('color', 'black', 'Black', 'rt-black')}{betButton('parity', 'odd', 'Odd')}{betButton('range', 'high', '19–36')}
          </div>
          <div className="rt-columns">
            {betButton('column', 1, 'Column 1')}{betButton('column', 2, 'Column 2')}{betButton('column', 3, 'Column 3')}
          </div>
        </section>
      </div>

      <div className="rt-console">
        <div className="rt-chip-rack" aria-label="Choose chip value">
          {STAKES.map((value) => <motion.button whileTap={{ scale: .9 }} type="button" key={value} className={stake === value ? 'is-selected' : ''} aria-pressed={stake === value} onClick={() => setStake(value)}>{value}</motion.button>)}
        </div>
        <div className="rt-current-bet"><span>Current bet</span><strong data-bet-total>{numberFormat.format(totalBet)} chips</strong></div>
        <div className="rt-status" data-tone={tone} aria-live="polite">{status}</div>
        <div className="rt-actions">
          <button type="button" disabled={!betHistory.length || spinning} onClick={undo}>Undo</button>
          <button type="button" disabled={!bets.size || spinning} onClick={clear}>Clear</button>
          <motion.button whileTap={{ scale: .96 }} className="rt-spin" data-spin type="button" disabled={!bets.size || spinning} onClick={spin}>{spinning ? 'Spinning…' : 'Spin'}</motion.button>
        </div>
        <div className="rt-recent" aria-label="Recent roulette results">
          <span>Recent</span>
          <ol>{recent.length ? recent.map((entry) => {
            const number = Number(entry.result);
            return <li key={entry.id} data-color={rouletteColor(number)}>{number}</li>;
          }) : <li className="is-empty">–</li>}</ol>
        </div>
      </div>
    </div>
  );
}
