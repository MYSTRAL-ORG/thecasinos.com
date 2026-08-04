export type RouletteColor = 'green' | 'red' | 'black';

export type RouletteBet = {
  key: string;
  kind: 'number' | 'color' | 'parity' | 'range' | 'dozen' | 'column';
  value: string | number;
  amount: number;
};

export const ROULETTE_RED_NUMBERS = new Set([
  1, 3, 5, 7, 9, 12, 14, 16, 18, 19, 21, 23, 25, 27, 30, 32, 34, 36,
]);

export function rouletteColor(number: number): RouletteColor {
  if (number === 0) return 'green';
  return ROULETTE_RED_NUMBERS.has(number) ? 'red' : 'black';
}

export function rouletteBetWins(bet: RouletteBet, result: number): boolean {
  if (!Number.isInteger(result) || result < 0 || result > 36) return false;

  switch (bet.kind) {
    case 'number':
      return Number(bet.value) === result;
    case 'color':
      return result !== 0 && rouletteColor(result) === bet.value;
    case 'parity':
      return result !== 0 && (bet.value === 'even' ? result % 2 === 0 : result % 2 === 1);
    case 'range':
      return result !== 0 && (bet.value === 'low' ? result <= 18 : result >= 19);
    case 'dozen': {
      if (result === 0) return false;
      const dozen = Math.ceil(result / 12);
      return dozen === Number(bet.value);
    }
    case 'column':
      return result !== 0 && ((result - 1) % 3) + 1 === Number(bet.value);
  }
}

export function rouletteGrossPayout(bet: RouletteBet, result: number): number {
  if (!rouletteBetWins(bet, result)) return 0;
  return bet.amount * (bet.kind === 'number' ? 36 : bet.kind === 'dozen' || bet.kind === 'column' ? 3 : 2);
}

export function rouletteRoundPayout(bets: Iterable<RouletteBet>, result: number): number {
  let payout = 0;
  for (const bet of bets) payout += rouletteGrossPayout(bet, result);
  return payout;
}

export function secureRouletteResult(): number {
  const range = 2 ** 32;
  const acceptedLimit = range - (range % 37);
  const random = new Uint32Array(1);
  do crypto.getRandomValues(random); while (random[0] >= acceptedLimit);
  return random[0] % 37;
}
