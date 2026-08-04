import type { TrainingWallet } from '@/lib/training-wallet';

export type RoomView = 'lobby' | 'roulette' | 'blackjack' | 'video-poker';

export type GameProps = {
  wallet: TrainingWallet;
  refreshWallet: (wallet?: TrainingWallet) => void;
  onBusyChange: (busy: boolean) => void;
};
