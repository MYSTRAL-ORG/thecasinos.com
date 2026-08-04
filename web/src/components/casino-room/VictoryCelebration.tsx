import { AnimatePresence, animate, motion, useReducedMotion } from 'motion/react';
import { useEffect, useId, useMemo, useState } from 'react';

type VictoryGame = 'roulette' | 'blackjack' | 'video-poker';
type VictoryAccent = 'lime' | 'red' | 'black' | 'green' | 'gold';

type VictoryCelebrationProps = {
  open: boolean;
  game: VictoryGame;
  accent?: VictoryAccent;
  emblem: string;
  title: string;
  detail: string;
  reward: number;
  rewardLabel: string;
  rewardPrefix?: string;
  primaryLabel: string;
  onPrimary: () => void;
  onClose: () => void;
};

const numberFormat = new Intl.NumberFormat('en-US');
const CONFETTI_COLORS = ['#d7e876', '#f4cf78', '#f6f3df', '#ef8f94', '#71caa7', '#6e9edb'];

const gameLabels: Record<VictoryGame, string> = {
  roulette: 'Roulette win',
  blackjack: 'Blackjack win',
  'video-poker': 'Video Poker win',
};

export default function VictoryCelebration({
  open,
  game,
  accent = 'lime',
  emblem,
  title,
  detail,
  reward,
  rewardLabel,
  rewardPrefix = '+',
  primaryLabel,
  onPrimary,
  onClose,
}: VictoryCelebrationProps) {
  const reduceMotion = useReducedMotion();
  const titleId = useId();
  const detailId = useId();
  const [displayReward, setDisplayReward] = useState(reward);
  const particles = useMemo(() => Array.from({ length: 56 }, (_, index) => {
    const fromRight = index % 2 === 1;
    const spread = 76 + (index * 31) % 245;
    return {
      id: index,
      fromRight,
      color: CONFETTI_COLORS[index % CONFETTI_COLORS.length],
      x: fromRight ? -spread : spread,
      peak: -(190 + (index * 19) % 185),
      landing: -(30 + (index * 11) % 92),
      rotate: (index % 2 ? -1 : 1) * (310 + (index * 47) % 620),
      delay: (index % 14) * .025,
      duration: .95 + (index % 7) * .075,
      round: index % 5 === 0,
    };
  }), []);

  useEffect(() => {
    if (!open) return;
    setDisplayReward(reduceMotion ? reward : 0);
    if (reduceMotion) return;
    const counter = animate(0, reward, {
      duration: .86,
      delay: .34,
      ease: [0.22, 1, 0.36, 1],
      onUpdate: (value) => setDisplayReward(Math.round(value)),
    });
    return () => counter.stop();
  }, [open, reduceMotion, reward]);

  useEffect(() => {
    if (!open) return;
    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === 'Escape') onClose();
    };
    window.addEventListener('keydown', onKeyDown);
    return () => window.removeEventListener('keydown', onKeyDown);
  }, [onClose, open]);

  return (
    <AnimatePresence>
      {open && (
        <motion.div
          className="victory-layer"
          data-victory-overlay
          data-game={game}
          data-accent={accent}
          initial={false}
          exit={{ transition: { duration: reduceMotion ? 0 : .2 } }}
        >
          <div className="victory-aurora" aria-hidden="true" />

          {!reduceMotion && (
            <div className="victory-confetti" aria-hidden="true">
              {particles.map((particle) => (
                <motion.i
                  key={particle.id}
                  data-round={particle.round}
                  style={{
                    left: particle.fromRight ? '88%' : '12%',
                    backgroundColor: particle.color,
                  }}
                  initial={{ x: 0, y: 0, rotate: 0, scale: .35 }}
                  animate={{
                    x: [0, particle.x * .58, particle.x],
                    y: [0, particle.peak, particle.landing],
                    rotate: [0, particle.rotate * .45, particle.rotate],
                    scale: [.35, 1, .82],
                  }}
                  transition={{
                    duration: particle.duration,
                    delay: particle.delay,
                    times: [0, .48, 1],
                    ease: [0.19, .72, .2, 1],
                  }}
                />
              ))}
            </div>
          )}

          <motion.section
            className="victory-card"
            role="dialog"
            aria-modal="true"
            aria-labelledby={titleId}
            aria-describedby={detailId}
            initial={reduceMotion ? false : { y: 38, scale: .72, rotateX: -13 }}
            animate={{ y: 0, scale: 1, rotateX: 0 }}
            exit={reduceMotion ? undefined : { y: 20, scale: .94 }}
            transition={{ type: 'spring', stiffness: 260, damping: 22, mass: .82 }}
          >
            <div className="victory-shimmer" aria-hidden="true" />
            <button className="victory-close" type="button" aria-label="Close victory screen" onClick={onClose}>×</button>

            <motion.div
              className="victory-emblem"
              initial={reduceMotion ? false : { scale: .2, rotate: -18 }}
              animate={{ scale: 1, rotate: 0 }}
              transition={{ type: 'spring', stiffness: 310, damping: 17, delay: .08 }}
              aria-hidden="true"
            >
              <motion.i animate={reduceMotion ? undefined : { rotate: 360 }} transition={{ duration: 11, repeat: Infinity, ease: 'linear' }} />
              <span>{emblem}</span>
            </motion.div>

            <motion.span className="victory-kicker" initial={reduceMotion ? false : { y: 10 }} animate={{ y: 0 }} transition={{ delay: .16 }}>{gameLabels[game]}</motion.span>
            <motion.h2 id={titleId} initial={reduceMotion ? false : { y: 13 }} animate={{ y: 0 }} transition={{ delay: .2 }}>Congratulations!</motion.h2>
            <motion.p className="victory-title" initial={reduceMotion ? false : { y: 12 }} animate={{ y: 0 }} transition={{ delay: .24 }}>{title}</motion.p>

            <motion.div
              className="victory-reward"
              initial={reduceMotion ? false : { y: 16, scale: .9 }}
              animate={{ y: 0, scale: 1 }}
              transition={{ type: 'spring', stiffness: 290, damping: 22, delay: .28 }}
            >
              <strong><small>{rewardPrefix}</small>{numberFormat.format(displayReward)}</strong>
              <span>{rewardLabel}</span>
            </motion.div>

            <p className="victory-detail" id={detailId}>{detail}</p>
            <div className="victory-actions">
              <motion.button autoFocus type="button" className="victory-primary" whileTap={{ scale: .96 }} onClick={onPrimary}>{primaryLabel}</motion.button>
              <button type="button" className="victory-secondary" onClick={onClose}>Review the table</button>
            </div>
          </motion.section>
        </motion.div>
      )}
    </AnimatePresence>
  );
}
