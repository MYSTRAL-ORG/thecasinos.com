export const BLACKJACK_SUITS = ['club', 'diamond', 'heart', 'spade'] as const;

export type BlackjackSuit = typeof BLACKJACK_SUITS[number];
export type BlackjackRank = 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | 11 | 12 | 13;
export type BlackjackAction = 'hit' | 'stand' | 'double' | 'split';

export type BlackjackCard = {
  suit: BlackjackSuit;
  rank: BlackjackRank;
};

export type BlackjackHandValue = {
  total: number;
  soft: boolean;
  bust: boolean;
  blackjack: boolean;
};

export type BlackjackSettlement = {
  payout: number;
  result: 'blackjack' | 'win' | 'push' | 'loss' | 'bust';
};

const SUIT_LABELS: Record<BlackjackSuit, string> = {
  club: 'clubs',
  diamond: 'diamonds',
  heart: 'hearts',
  spade: 'spades',
};

const RANK_LABELS: Record<BlackjackRank, string> = {
  1: 'Ace',
  2: '2',
  3: '3',
  4: '4',
  5: '5',
  6: '6',
  7: '7',
  8: '8',
  9: '9',
  10: '10',
  11: 'Jack',
  12: 'Queen',
  13: 'King',
};

export function buildBlackjackShoe(decks = 6): BlackjackCard[] {
  const safeDecks = Math.max(1, Math.min(8, Math.floor(decks)));
  return Array.from({ length: safeDecks }, () =>
    BLACKJACK_SUITS.flatMap((suit) =>
      Array.from({ length: 13 }, (_, index) => ({ suit, rank: (index + 1) as BlackjackRank })),
    ),
  ).flat();
}

export function shuffledBlackjackShoe(decks = 6): BlackjackCard[] {
  const shoe = buildBlackjackShoe(decks);
  for (let index = shoe.length - 1; index > 0; index -= 1) {
    const values = new Uint32Array(1);
    crypto.getRandomValues(values);
    const swapIndex = values[0] % (index + 1);
    [shoe[index], shoe[swapIndex]] = [shoe[swapIndex], shoe[index]];
  }
  return shoe;
}

export function blackjackCardPath(card: BlackjackCard) {
  return `/cards/${card.suit}/${card.rank}.png`;
}

export function blackjackCardLabel(card: BlackjackCard) {
  return `${RANK_LABELS[card.rank]} of ${SUIT_LABELS[card.suit]}`;
}

export function blackjackCardPoints(card: BlackjackCard) {
  if (card.rank === 1) return 11;
  return Math.min(card.rank, 10);
}

export function blackjackHandValue(cards: BlackjackCard[]): BlackjackHandValue {
  let total = cards.reduce((sum, card) => sum + blackjackCardPoints(card), 0);
  let acesAsEleven = cards.filter((card) => card.rank === 1).length;
  while (total > 21 && acesAsEleven > 0) {
    total -= 10;
    acesAsEleven -= 1;
  }
  return {
    total,
    soft: acesAsEleven > 0,
    bust: total > 21,
    blackjack: cards.length === 2 && total === 21,
  };
}

export function dealerShouldHit(cards: BlackjackCard[]) {
  return blackjackHandValue(cards).total < 17;
}

export function canSplitBlackjackHand(cards: BlackjackCard[]) {
  return cards.length === 2 && cards[0]?.rank === cards[1]?.rank;
}

export function settleBlackjackHand(
  playerCards: BlackjackCard[],
  dealerCards: BlackjackCard[],
  stake: number,
  naturalEligible = true,
): BlackjackSettlement {
  const player = blackjackHandValue(playerCards);
  const dealer = blackjackHandValue(dealerCards);
  const playerNatural = naturalEligible && player.blackjack;
  const dealerNatural = dealer.blackjack;

  if (player.bust) return { payout: 0, result: 'bust' };
  if (playerNatural && dealerNatural) return { payout: stake, result: 'push' };
  if (playerNatural) return { payout: Math.floor(stake * 2.5), result: 'blackjack' };
  if (dealerNatural) return { payout: 0, result: 'loss' };
  if (dealer.bust || player.total > dealer.total) return { payout: stake * 2, result: 'win' };
  if (player.total === dealer.total) return { payout: stake, result: 'push' };
  return { payout: 0, result: 'loss' };
}

export function recommendedBlackjackAction(
  cards: BlackjackCard[],
  dealerUpCard: BlackjackCard,
  options: { canDouble?: boolean; canSplit?: boolean } = {},
): BlackjackAction {
  const dealer = blackjackCardPoints(dealerUpCard);
  const value = blackjackHandValue(cards);
  const canDouble = Boolean(options.canDouble);

  if (options.canSplit && canSplitBlackjackHand(cards)) {
    const rank = cards[0].rank;
    if (rank === 1 || rank === 8) return 'split';
    if (rank === 10 || rank > 10) return 'stand';
    if (rank === 9) return dealer === 7 || dealer >= 10 ? 'stand' : 'split';
    if (rank === 7 && dealer <= 7) return 'split';
    if (rank === 6 && dealer >= 2 && dealer <= 6) return 'split';
    if (rank === 5) return canDouble && dealer <= 9 ? 'double' : 'hit';
    if (rank === 4 && (dealer === 5 || dealer === 6)) return 'split';
    if ((rank === 2 || rank === 3) && dealer <= 7) return 'split';
  }

  if (value.soft && cards.length >= 2) {
    if (value.total >= 19) return 'stand';
    if (value.total === 18) {
      if (canDouble && dealer >= 3 && dealer <= 6) return 'double';
      return dealer >= 9 || dealer === 11 ? 'hit' : 'stand';
    }
    if (canDouble && value.total === 17 && dealer >= 3 && dealer <= 6) return 'double';
    if (canDouble && (value.total === 15 || value.total === 16) && dealer >= 4 && dealer <= 6) return 'double';
    if (canDouble && (value.total === 13 || value.total === 14) && dealer >= 5 && dealer <= 6) return 'double';
    return 'hit';
  }

  if (value.total >= 17) return 'stand';
  if (value.total >= 13) return dealer <= 6 ? 'stand' : 'hit';
  if (value.total === 12) return dealer >= 4 && dealer <= 6 ? 'stand' : 'hit';
  if (value.total === 11) return canDouble && dealer !== 11 ? 'double' : 'hit';
  if (value.total === 10) return canDouble && dealer <= 9 ? 'double' : 'hit';
  if (value.total === 9) return canDouble && dealer >= 3 && dealer <= 6 ? 'double' : 'hit';
  return 'hit';
}
