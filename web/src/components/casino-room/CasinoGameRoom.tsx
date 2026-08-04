import { AnimatePresence, motion, useReducedMotion } from 'motion/react';
import { useEffect, useState } from 'react';
import { BLACKJACK_SUITS } from '@/lib/blackjack';
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
import VideoPokerTable from './VideoPokerTable';
import type { RoomView } from './types';
import './casino-game-room.css';

const viewTitles: Record<RoomView, string> = { lobby: 'Casino lobby', roulette: 'European roulette', blackjack: 'Blackjack', 'video-poker': 'Jacks or Better' };
const CARD_ASSETS = [
  '/cards/back-cards.png',
  ...BLACKJACK_SUITS.flatMap((suit) => Array.from({ length: 13 }, (_, index) => `/cards/${suit}/${index + 1}.png`)),
];
const initialGameStats = () => ({ rounds: 0, winningRounds: 0, totalWagered: 0, totalReturned: 0 });
const initialWallet = (): TrainingWallet => ({
  version: 1,
  balance: TRAINING_STARTING_BALANCE,
  rounds: 0,
  winningRounds: 0,
  totalWagered: 0,
  totalReturned: 0,
  gameStats: { roulette: initialGameStats(), blackjack: initialGameStats(), 'video-poker': initialGameStats() },
  lastDailyBonus: null,
  history: [],
});

export default function CasinoGameRoom() {
  const reduceMotion = useReducedMotion();
  const [wallet, setWallet] = useState<TrainingWallet>(initialWallet);
  const [activeView, setActiveView] = useState<RoomView>('lobby');
  const [busyGame, setBusyGame] = useState<RoomView | null>(null);
  const [assetsReady, setAssetsReady] = useState(false);
  const [loadProgress, setLoadProgress] = useState(0);

  const refreshWallet = (nextWallet = readTrainingWallet()) => setWallet({ ...nextWallet });

  useEffect(() => {
    let cancelled = false;
    let loadedAssets = 0;
    const preload = (source: string) => new Promise<void>((resolve) => {
      const image = new Image();
      let settled = false;
      const timeout = window.setTimeout(finish, 8_000);

      function finish() {
        if (settled) return;
        settled = true;
        window.clearTimeout(timeout);
        image.onload = null;
        image.onerror = null;
        image.decode().catch(() => undefined).finally(resolve);
      }

      image.decoding = 'async';
      image.onload = finish;
      image.onerror = finish;
      image.src = source;
      if (image.complete) finish();
    });
    const prepareRoom = async () => {
      await Promise.all([
        ...CARD_ASSETS.map(async (source) => {
          await preload(source);
          loadedAssets += 1;
          if (!cancelled) setLoadProgress(Math.round((loadedAssets / CARD_ASSETS.length) * 100));
        }),
        new Promise((resolve) => window.setTimeout(resolve, reduceMotion ? 20 : 520)),
      ]);
      if (!cancelled) setAssetsReady(true);
    };

    const syncWallet = (event: Event) => refreshWallet((event as CustomEvent<TrainingWallet>).detail || readTrainingWallet());
    const syncStorage = () => refreshWallet();
    const requestedView = window.location.hash.slice(1);
    refreshWallet();
    if (requestedView === 'roulette' || requestedView === 'blackjack' || requestedView === 'video-poker') setActiveView(requestedView);
    window.addEventListener(TRAINING_WALLET_EVENT, syncWallet);
    window.addEventListener('storage', syncStorage);
    prepareRoom();
    return () => {
      cancelled = true;
      window.removeEventListener(TRAINING_WALLET_EVENT, syncWallet);
      window.removeEventListener('storage', syncStorage);
    };
  }, [reduceMotion]);

  const openView = (view: RoomView) => {
    if (!assetsReady || busyGame || view === activeView) return;
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
    x: activeView === view ? 0 : view === 'lobby' ? -22 : 22,
    scale: activeView === view ? 1 : .992,
  });

  return (
    <section className="casino-game-room" id="training-room" data-casino-room data-active-view={activeView} aria-label="Casino training room" aria-busy={!assetsReady}>
      <motion.div className="cgr-frame" data-ready={assetsReady} initial={reduceMotion ? false : { y: 18, scale: .99 }} whileInView={{ y: 0, scale: 1 }} viewport={{ once: true, amount: .1 }} transition={{ duration: .48, ease: [0.22, 1, 0.36, 1] }}>
        <AnimatePresence>
          {!assetsReady && (
            <motion.div className="cgr-loading" role="status" aria-live="polite" initial={{ opacity: 1 }} exit={{ opacity: 0 }} transition={{ duration: reduceMotion ? .01 : .28 }}>
              <motion.div className="cgr-loading-mark" aria-hidden="true" animate={reduceMotion ? undefined : { rotateY: [0, 180, 360] }} transition={{ duration: 1.8, repeat: Infinity, ease: [0.65, 0, 0.35, 1] }}><span>T</span></motion.div>
              <div className="cgr-loading-copy"><small>Training room</small><strong>Preparing the tables</strong><span>Loading cards · {loadProgress}%</span></div>
              <div className="cgr-loading-track" aria-hidden="true"><motion.i animate={{ scaleX: Math.max(.025, loadProgress / 100) }} transition={{ duration: reduceMotion ? .01 : .2 }} /></div>
            </motion.div>
          )}
        </AnimatePresence>
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
          <motion.section className="cgr-panel" data-view="video-poker" aria-hidden={activeView !== 'video-poker'} animate={panelMotion('video-poker')} transition={{ duration: reduceMotion ? .01 : .3, ease: [0.22, 1, 0.36, 1] }} style={{ pointerEvents: activeView === 'video-poker' ? 'auto' : 'none', visibility: activeView === 'video-poker' ? 'visible' : 'hidden' }}>
            <VideoPokerTable wallet={wallet} refreshWallet={refreshWallet} onBusyChange={gameBusyHandler('video-poker')} />
          </motion.section>
        </main>

        <nav className="cgr-dock" aria-label="Training room games">
          <button type="button" data-room-nav="lobby" data-active={activeView === 'lobby'} disabled={!assetsReady || Boolean(busyGame)} onClick={() => openView('lobby')}><span className="cgr-lobby-icon" aria-hidden="true"><i /><i /><i /><i /></span><b>Lobby</b></button>
          <button type="button" data-room-nav="roulette" data-active={activeView === 'roulette'} disabled={!assetsReady || Boolean(busyGame && activeView !== 'roulette')} onClick={() => openView('roulette')}><span className="cgr-wheel-icon" aria-hidden="true" /><b>Roulette</b></button>
          <button type="button" data-room-nav="blackjack" data-active={activeView === 'blackjack'} disabled={!assetsReady || Boolean(busyGame && activeView !== 'blackjack')} onClick={() => openView('blackjack')}><span className="cgr-cards-icon" aria-hidden="true"><i /><i /></span><b>Blackjack</b></button>
          <button type="button" data-room-nav="video-poker" data-active={activeView === 'video-poker'} disabled={!assetsReady || Boolean(busyGame && activeView !== 'video-poker')} onClick={() => openView('video-poker')}><span className="cgr-poker-icon" aria-hidden="true"><i /><i /><i /></span><b>Video poker</b></button>
        </nav>
      </motion.div>
    </section>
  );
}
