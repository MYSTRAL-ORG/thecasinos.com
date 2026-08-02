export type Casino = {
  id: string | number;
  legacy_id?: number | null;
  name: string;
  slug: string;
  country_name: string;
  country_slug: string;
  city_name: string;
  city_slug: string;
  state_name?: string | null;
  short_description?: string | null;
  description?: string | null;
  opened_on?: string | null;
  gaming_machines?: number | null;
  poker_tables?: number | null;
  table_games?: number | null;
  square_footage?: number | null;
  hotel_name?: string | null;
  owner_name?: string | null;
  always_open?: boolean | null;
  has_sportsbook?: boolean;
  has_bingo?: boolean;
  has_slots?: boolean;
  has_table_games?: boolean;
  longitude?: number | null;
  latitude?: number | null;
  published?: boolean;
};

export type OnlineCasino = {
  id: string;
  position: number | null;
  name: string;
  slug: string;
  subtitle?: string | null;
  rating: number;
  bonus: string;
  summary: string;
  description?: string | null;
  bonus_description?: string | null;
  deposit_description?: string | null;
  contact_description?: string | null;
  affiliate_url: string;
  logo_url?: string | null;
  key_features: string[];
  pros: string[];
  cons: string[];
  deposit_methods: string[];
  contact_methods: string[];
  active: boolean;
  published: boolean;
  affiliate_disclosure?: string | null;
};

export type Destination = {
  country_name: string;
  country_slug: string;
  casino_count: number;
};
