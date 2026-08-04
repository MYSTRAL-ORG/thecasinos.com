export type VideoPokerSuit = 'club' | 'diamond' | 'heart' | 'spade';

export type VideoPokerCard = {
  suit: VideoPokerSuit;
  rank: number;
};

export type VideoPokerHandKey =
  | 'royal-flush'
  | 'straight-flush'
  | 'four-kind'
  | 'full-house'
  | 'flush'
  | 'straight'
  | 'three-kind'
  | 'two-pair'
  | 'jacks-or-better'
  | 'nothing';

export type VideoPokerResult = {
  key: VideoPokerHandKey;
  label: string;
  multiplier: number;
};

export const VIDEO_POKER_CREDIT_VALUE = 10;

export const VIDEO_POKER_PAYTABLE: VideoPokerResult[] = [
  { key: 'royal-flush', label: 'Royal flush', multiplier: 250 },
  { key: 'straight-flush', label: 'Straight flush', multiplier: 50 },
  { key: 'four-kind', label: 'Four of a kind', multiplier: 25 },
  { key: 'full-house', label: 'Full house', multiplier: 9 },
  { key: 'flush', label: 'Flush', multiplier: 6 },
  { key: 'straight', label: 'Straight', multiplier: 4 },
  { key: 'three-kind', label: 'Three of a kind', multiplier: 3 },
  { key: 'two-pair', label: 'Two pair', multiplier: 2 },
  { key: 'jacks-or-better', label: 'Jacks or better', multiplier: 1 },
];

const NOTHING: VideoPokerResult = { key: 'nothing', label: 'No win', multiplier: 0 };

export function createVideoPokerDeck(): VideoPokerCard[] {
  const suits: VideoPokerSuit[] = ['club', 'diamond', 'heart', 'spade'];
  return suits.flatMap((suit) => Array.from({ length: 13 }, (_, index) => ({ suit, rank: index + 1 })));
}

function randomIndex(maximum: number) {
  if (maximum <= 1) return 0;
  if (typeof crypto !== 'undefined' && typeof crypto.getRandomValues === 'function') {
    const limit = Math.floor(0x1_0000_0000 / maximum) * maximum;
    const value = new Uint32Array(1);
    do crypto.getRandomValues(value); while (value[0] >= limit);
    return value[0] % maximum;
  }
  return Math.floor(Math.random() * maximum);
}

export function shuffledVideoPokerDeck() {
  const deck = createVideoPokerDeck();
  for (let index = deck.length - 1; index > 0; index -= 1) {
    const swapIndex = randomIndex(index + 1);
    [deck[index], deck[swapIndex]] = [deck[swapIndex], deck[index]];
  }
  return deck;
}

export function videoPokerCardImage(card: VideoPokerCard) {
  return `/cards/${card.suit}/${card.rank}.png`;
}

export function videoPokerCardLabel(card: VideoPokerCard) {
  const rank = card.rank === 1 ? 'Ace' : card.rank === 11 ? 'Jack' : card.rank === 12 ? 'Queen' : card.rank === 13 ? 'King' : String(card.rank);
  const suit = card.suit === 'club' ? 'clubs' : card.suit === 'diamond' ? 'diamonds' : card.suit === 'heart' ? 'hearts' : 'spades';
  return `${rank} of ${suit}`;
}

export function evaluateJacksOrBetter(cards: VideoPokerCard[]): VideoPokerResult {
  if (cards.length !== 5) return NOTHING;

  const ranks = cards.map((card) => card.rank).sort((a, b) => a - b);
  const uniqueRanks = [...new Set(ranks)];
  const flush = cards.every((card) => card.suit === cards[0].suit);
  const standardStraight = uniqueRanks.length === 5 && uniqueRanks[4] - uniqueRanks[0] === 4;
  const wheelStraight = uniqueRanks.join(',') === '1,2,3,4,5';
  const royalStraight = uniqueRanks.join(',') === '1,10,11,12,13';
  const straight = standardStraight || wheelStraight || royalStraight;

  const groups = [...ranks.reduce((counts, rank) => counts.set(rank, (counts.get(rank) ?? 0) + 1), new Map<number, number>())]
    .sort((left, right) => right[1] - left[1] || right[0] - left[0]);
  const groupSizes = groups.map(([, count]) => count);

  if (flush && royalStraight) return VIDEO_POKER_PAYTABLE[0];
  if (flush && straight) return VIDEO_POKER_PAYTABLE[1];
  if (groupSizes[0] === 4) return VIDEO_POKER_PAYTABLE[2];
  if (groupSizes[0] === 3 && groupSizes[1] === 2) return VIDEO_POKER_PAYTABLE[3];
  if (flush) return VIDEO_POKER_PAYTABLE[4];
  if (straight) return VIDEO_POKER_PAYTABLE[5];
  if (groupSizes[0] === 3) return VIDEO_POKER_PAYTABLE[6];
  if (groupSizes[0] === 2 && groupSizes[1] === 2) return VIDEO_POKER_PAYTABLE[7];
  if (groupSizes[0] === 2 && (groups[0][0] === 1 || groups[0][0] >= 11)) return VIDEO_POKER_PAYTABLE[8];
  return NOTHING;
}

export function videoPokerPayoutCredits(result: VideoPokerResult, credits: number) {
  const safeCredits = Math.min(5, Math.max(1, Math.floor(credits)));
  if (result.key === 'royal-flush' && safeCredits === 5) return 4_000;
  return result.multiplier * safeCredits;
}
