import { motion, useReducedMotion } from 'motion/react';
import { useEffect, useState } from 'react';
import {
  canClaimTrainingBonus,
  claimTrainingBonus,
  readTrainingWallet,
  TRAINING_STARTING_BALANCE,
  TRAINING_WALLET_EVENT,
  type TrainingWallet,
} from '@/lib/training-wallet';
import BlackjackTable from './BlackjackTable';
import CasinoLobby from './CasinoLobby';
import RouletteTable from './RouletteTable';
import type { RoomView } from './types';
import './casino-game-room.css';

const viewTitles: Record<RoomView, string> = { lobby: 'Casino lobby', roulette: 'European roulette', blackjack: 'Blackjack' };
const initialGameStats = () => ({ rounds: 0, winningRounds: 0, totalWagered: 0, totalReturned: 0 });
const initialWallet = (): TrainingWallet => ({
  version: 1,
  balance: TRAINING_STARTING_BALANCE,
  rounds: 0,
  winningRounds: 0,
  totalWagered: 0,
  totalReturned: 0,
  gameStats: { roulette: initialGameStats(), blackjack: initialGameStats() },
  lastDailyBonus: null,
  history: [],
});

export default function CasinoGameRoom() {
  const reduceMotion = useReducedMotion();
  const [wallet, setWallet] = useState<TrainingWallet>(initialWallet);
  const [activeView, setActiveView] = useState<RoomView>('lobby');
  const [busyGame, setBusyGame] = useState<RoomView | null>(null);

  const refreshWallet = (nextWallet = readTrainingWallet()) => setWallet({ ...nextWallet });

  useEffect(() => {
    const syncWallet = (event: Event) => refreshWallet((event as CustomEvent<TrainingWallet>).detail || readTrainingWallet());
    const syncStorage = () => refreshWallet();
    const requestedView = window.location.hash.slice(1);
    refreshWallet();
    if (requestedView === 'roulette' || requestedView === 'blackjack') setActiveView(requestedView);
    window.addEventListener(TRAINING_WALLET_EVENT, syncWallet);
    window.addEventListener('storage', syncStorage);
    return () => {
      window.removeEventListener(TRAINING_WALLET_EVENT, syncWallet);
      window.removeEventListener('storage', syncStorage);
    };
  }, []);

  const openView = (view: RoomView) => {
    if (busyGame || view === activeView) return;
    setActiveView(view);
    const target = view === 'lobby' ? `${window.location.pathname}${window.location.search}` : `#${view}`;
    window.history.replaceState(null, '', target);
  };

  const claimBonus = () => {
    const result = claimTrainingBonus();
    refreshWallet(result.wallet);
  };

  const gameBusyHandler = (game: Exclude<RoomView, 'lobby'>) => (busy: boolean) => {
    setBusyGame((current) => busy ? game : current === game ? null : current);
  };
  const panelMotion = (view: RoomView) => ({
    opacity: activeView === view ? 1 : 0,
    x: activeView === view ? 0 : view === 'lobby' ? -22 : 22,
    scale: activeView === view ? 1 : .992,
  });

  return (
    <section className="casino-game-room" id="training-room" data-casino-room data-active-view={activeView} aria-label="Casino training room">
      <motion.div className="cgr-frame" initial={reduceMotion ? false : { opacity: 0, y: 18, scale: .99 }} whileInView={{ opacity: 1, y: 0, scale: 1 }} viewport={{ once: true, amount: .1 }} transition={{ duration: .48, ease: [0.22, 1, 0.36, 1] }}>
        <header className="cgr-topbar">
          <button type="button" className="cgr-back" data-room-lobby onClick={() => openView('lobby')} disabled={Boolean(busyGame)} aria-label="Return to the casino lobby" data-visible={activeView !== 'lobby'}>
            <span aria-hidden="true">‹</span><b>Games</b>
          </button>
          <div className="cgr-brand"><i aria-hidden="true">T</i><div><small>Free play</small><strong>{viewTitles[activeView]}</strong></div></div>
          <output className="cgr-wallet" aria-label={`${wallet.balance} practice chips`}><i aria-hidden="true" /><strong>{wallet.balance.toLocaleString('en-US')}</strong><small>chips</small></output>
        </header>

        <main className="cgr-stage">
          <motion.section className="cgr-panel" data-view="lobby" aria-hidden={activeView !== 'lobby'} animate={panelMotion('lobby')} transition={{ duration: reduceMotion ? .01 : .3, ease: [0.22, 1, 0.36, 1] }} style={{ pointerEvents: activeView === 'lobby' ? 'auto' : 'none', visibility: activeView === 'lobby' ? 'visible' : 'hidden' }}>
            <CasinoLobby wallet={wallet} canClaimBonus={canClaimTrainingBonus(wallet)} onClaimBonus={claimBonus} onOpen={openView} />
          </motion.section>
          <motion.section className="cgr-panel" data-view="roulette" aria-hidden={activeView !== 'roulette'} animate={panelMotion('roulette')} transition={{ duration: reduceMotion ? .01 : .3, ease: [0.22, 1, 0.36, 1] }} style={{ pointerEvents: activeView === 'roulette' ? 'auto' : 'none', visibility: activeView === 'roulette' ? 'visible' : 'hidden' }}>
            <RouletteTable wallet={wallet} refreshWallet={refreshWallet} onBusyChange={gameBusyHandler('roulette')} />
          </motion.section>
          <motion.section className="cgr-panel" data-view="blackjack" aria-hidden={activeView !== 'blackjack'} animate={panelMotion('blackjack')} transition={{ duration: reduceMotion ? .01 : .3, ease: [0.22, 1, 0.36, 1] }} style={{ pointerEvents: activeView === 'blackjack' ? 'auto' : 'none', visibility: activeView === 'blackjack' ? 'visible' : 'hidden' }}>
            <BlackjackTable wallet={wallet} refreshWallet={refreshWallet} onBusyChange={gameBusyHandler('blackjack')} />
          </motion.section>
        </main>

        <nav className="cgr-dock" aria-label="Training room games">
          <button type="button" data-room-nav="lobby" data-active={activeView === 'lobby'} disabled={Boolean(busyGame)} onClick={() => openView('lobby')}><span className="cgr-lobby-icon" aria-hidden="true"><i /><i /><i /><i /></span><b>Lobby</b></button>
          <button type="button" data-room-nav="roulette" data-active={activeView === 'roulette'} disabled={Boolean(busyGame && activeView !== 'roulette')} onClick={() => openView('roulette')}><span className="cgr-wheel-icon" aria-hidden="true" /><b>Roulette</b></button>
          <button type="button" data-room-nav="blackjack" data-active={activeView === 'blackjack'} disabled={Boolean(busyGame && activeView !== 'blackjack')} onClick={() => openView('blackjack')}><span className="cgr-cards-icon" aria-hidden="true"><i /><i /></span><b>Blackjack</b></button>
        </nav>
      </motion.div>
    </section>
  );
}
