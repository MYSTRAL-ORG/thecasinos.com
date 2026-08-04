import { motion } from 'motion/react';
import type { CSSProperties } from 'react';
import type { TrainingWallet } from '@/lib/training-wallet';
import type { RoomView } from './types';

type Props = {
  wallet: TrainingWallet;
  canClaimBonus: boolean;
  onClaimBonus: () => void;
  onOpen: (view: Exclude<RoomView, 'lobby'>) => void;
};

const tileMotion = {
  initial: { opacity: 0, y: 18 },
  animate: { opacity: 1, y: 0 },
  whileHover: { y: -4 },
  transition: { type: 'spring' as const, stiffness: 280, damping: 24 },
};

export default function CasinoLobby({ wallet, canClaimBonus, onClaimBonus, onOpen }: Props) {
  return (
    <div className="cgr-lobby">
      <div className="cgr-lobby-heading">
        <div>
          <span className="cgr-kicker">Training room</span>
          <h2>Choose your table.</h2>
          <p>Real rules, fictional chips, no registration.</p>
        </div>
        <button className="cgr-daily" type="button" disabled={!canClaimBonus} onClick={onClaimBonus}>
          <span>Daily practice bonus</span>
          <strong>{canClaimBonus ? '+500 chips' : 'Claimed today'}</strong>
        </button>
      </div>

      <div className="cgr-game-grid">
        <motion.button {...tileMotion} className="cgr-game-card cgr-game-card-live" data-open-game="roulette" type="button" onClick={() => onOpen('roulette')}>
          <div className="cgr-game-art cgr-roulette-art" aria-hidden="true">
            <motion.div className="cgr-mini-wheel" animate={{ rotate: 360 }} transition={{ duration: 18, repeat: Infinity, ease: 'linear' }}>
              <span>0</span>
            </motion.div>
            <i className="cgr-mini-ball" />
          </div>
          <div className="cgr-game-copy">
            <span>European · 0–36</span>
            <h3>Roulette</h3>
            <p>Build a bet, spin the wheel and learn every payout.</p>
            <small>{wallet.gameStats.roulette.rounds} rounds played</small>
            <b>Play now <span aria-hidden="true">→</span></b>
          </div>
        </motion.button>

        <motion.button {...tileMotion} transition={{ ...tileMotion.transition, delay: .05 }} className="cgr-game-card cgr-game-card-live" data-open-game="blackjack" type="button" onClick={() => onOpen('blackjack')}>
          <div className="cgr-game-art cgr-blackjack-art" aria-hidden="true">
            <motion.img src="/cards/spade/1.png" alt="" initial={{ rotate: -13, x: -14, y: 8 }} whileHover={{ rotate: -18, x: -22 }} style={{ '--preview-card': 0 } as CSSProperties} />
            <motion.img src="/cards/heart/13.png" alt="" initial={{ rotate: 10, x: 18, y: -2 }} whileHover={{ rotate: 15, x: 27 }} />
            <span className="cgr-preview-chip">50</span>
          </div>
          <div className="cgr-game-copy">
            <span>Blackjack pays 3:2</span>
            <h3>Blackjack</h3>
            <p>Play every decision with a live basic-strategy coach.</p>
            <small>{wallet.gameStats.blackjack.rounds} hands played</small>
            <b>Take a seat <span aria-hidden="true">→</span></b>
          </div>
        </motion.button>

        <motion.article {...tileMotion} transition={{ ...tileMotion.transition, delay: .1 }} className="cgr-game-card cgr-game-card-soon">
          <div className="cgr-soon-cards" aria-hidden="true">
            <img src="/cards/diamond/10.png" alt="" /><img src="/cards/diamond/11.png" alt="" /><img src="/cards/diamond/12.png" alt="" />
          </div>
          <div><span>Next table</span><h3>Video poker</h3></div><b>Coming soon</b>
        </motion.article>

        <motion.article {...tileMotion} transition={{ ...tileMotion.transition, delay: .15 }} className="cgr-game-card cgr-game-card-soon">
          <div className="cgr-slot-reels" aria-hidden="true"><i>7</i><i>◆</i><i>★</i></div>
          <div><span>Probability lab</span><h3>Slots</h3></div><b>Planned</b>
        </motion.article>
      </div>
    </div>
  );
}
