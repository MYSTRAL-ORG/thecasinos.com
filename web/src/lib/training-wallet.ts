export type TrainingHistoryEntry = {
  id: string;
  game: TrainingGame;
  result: string;
  wagered: number;
  payout: number;
  net: number;
  playedAt: string;
};

export type TrainingGame = 'roulette' | 'blackjack' | 'video-poker';

export type TrainingGameStats = {
  rounds: number;
  winningRounds: number;
  totalWagered: number;
  totalReturned: number;
};

export type TrainingWallet = {
  version: 1;
  balance: number;
  rounds: number;
  winningRounds: number;
  totalWagered: number;
  totalReturned: number;
  gameStats: Record<TrainingGame, TrainingGameStats>;
  lastDailyBonus: string | null;
  history: TrainingHistoryEntry[];
};

export const TRAINING_STARTING_BALANCE = 2_000;
export const TRAINING_DAILY_BONUS = 500;
export const TRAINING_WALLET_EVENT = 'thecasinos:training-wallet';
const TRAINING_WALLET_KEY = 'thecasinos.training-wallet.v1';

function emptyGameStats(): TrainingGameStats {
  return { rounds: 0, winningRounds: 0, totalWagered: 0, totalReturned: 0 };
}

function freshWallet(): TrainingWallet {
  return {
    version: 1,
    balance: TRAINING_STARTING_BALANCE,
    rounds: 0,
    winningRounds: 0,
    totalWagered: 0,
    totalReturned: 0,
    gameStats: {
      roulette: emptyGameStats(),
      blackjack: emptyGameStats(),
      'video-poker': emptyGameStats(),
    },
    lastDailyBonus: null,
    history: [],
  };
}

function safeInteger(value: unknown, fallback = 0, maximum = 999_999_999) {
  const number = Number(value);
  return Number.isFinite(number) ? Math.min(maximum, Math.max(0, Math.floor(number))) : fallback;
}

function normalizeWallet(value: Partial<TrainingWallet> | null | undefined): TrainingWallet {
  const fallback = freshWallet();
  if (!value || value.version !== 1) return fallback;
  const legacyRouletteStats = {
    rounds: safeInteger(value.rounds),
    winningRounds: safeInteger(value.winningRounds),
    totalWagered: safeInteger(value.totalWagered),
    totalReturned: safeInteger(value.totalReturned),
  };
  const normalizedGameStats = (game: TrainingGame, legacy: TrainingGameStats): TrainingGameStats => {
    const stats = value.gameStats?.[game];
    if (!stats) return legacy;
    return {
      rounds: safeInteger(stats.rounds),
      winningRounds: safeInteger(stats.winningRounds),
      totalWagered: safeInteger(stats.totalWagered),
      totalReturned: safeInteger(stats.totalReturned),
    };
  };
  return {
    version: 1,
    balance: safeInteger(value.balance, fallback.balance),
    rounds: safeInteger(value.rounds),
    winningRounds: safeInteger(value.winningRounds),
    totalWagered: safeInteger(value.totalWagered),
    totalReturned: safeInteger(value.totalReturned),
    gameStats: {
      roulette: normalizedGameStats('roulette', legacyRouletteStats),
      blackjack: normalizedGameStats('blackjack', emptyGameStats()),
      'video-poker': normalizedGameStats('video-poker', emptyGameStats()),
    },
    lastDailyBonus: typeof value.lastDailyBonus === 'string' ? value.lastDailyBonus : null,
    history: Array.isArray(value.history)
      ? value.history.slice(0, 12).filter((entry): entry is TrainingHistoryEntry => Boolean(
        entry
        && (entry.game === 'roulette' || entry.game === 'blackjack' || entry.game === 'video-poker')
        && typeof entry.result === 'string'
        && typeof entry.playedAt === 'string',
      ))
      : [],
  };
}

function todayKey() {
  const today = new Date();
  const year = today.getFullYear();
  const month = String(today.getMonth() + 1).padStart(2, '0');
  const day = String(today.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

export function readTrainingWallet(): TrainingWallet {
  if (typeof localStorage === 'undefined') return freshWallet();
  try {
    return normalizeWallet(JSON.parse(localStorage.getItem(TRAINING_WALLET_KEY) || 'null'));
  } catch {
    return freshWallet();
  }
}

function saveTrainingWallet(wallet: TrainingWallet): TrainingWallet {
  if (typeof localStorage !== 'undefined') {
    try {
      localStorage.setItem(TRAINING_WALLET_KEY, JSON.stringify(wallet));
    } catch {
      // The game remains playable when private browsing blocks persistent storage.
    }
  }
  if (typeof window !== 'undefined') window.dispatchEvent(new CustomEvent(TRAINING_WALLET_EVENT, { detail: wallet }));
  return wallet;
}

export function canClaimTrainingBonus(wallet = readTrainingWallet()) {
  return wallet.lastDailyBonus !== todayKey();
}

export function claimTrainingBonus() {
  const wallet = readTrainingWallet();
  if (!canClaimTrainingBonus(wallet)) return { claimed: false, wallet };
  wallet.balance += TRAINING_DAILY_BONUS;
  wallet.lastDailyBonus = todayKey();
  return { claimed: true, wallet: saveTrainingWallet(wallet) };
}

export function completeTrainingRound(input: {
  game?: TrainingGame;
  result: string;
  wagered: number;
  payout: number;
}) {
  const wallet = readTrainingWallet();
  const wagered = safeInteger(input.wagered);
  const payout = safeInteger(input.payout);
  if (wagered <= 0 || wagered > wallet.balance) return { completed: false, wallet };

  const net = payout - wagered;
  wallet.balance = wallet.balance - wagered + payout;
  wallet.rounds += 1;
  if (net > 0) wallet.winningRounds += 1;
  wallet.totalWagered += wagered;
  wallet.totalReturned += payout;
  const game = input.game ?? 'roulette';
  const gameStats = wallet.gameStats[game];
  gameStats.rounds += 1;
  if (net > 0) gameStats.winningRounds += 1;
  gameStats.totalWagered += wagered;
  gameStats.totalReturned += payout;
  wallet.history.unshift({
    id: `${Date.now()}-${Math.random().toString(36).slice(2, 8)}`,
    game,
    result: input.result,
    wagered,
    payout,
    net,
    playedAt: new Date().toISOString(),
  });
  wallet.history = wallet.history.slice(0, 12);
  return { completed: true, wallet: saveTrainingWallet(wallet) };
}

export function resetTrainingWallet() {
  return saveTrainingWallet(freshWallet());
}
