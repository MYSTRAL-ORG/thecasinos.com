alter table public.casinos
  add column if not exists profile_version smallint not null default 1,
  add column if not exists key_facts jsonb not null default '[]'::jsonb,
  add column if not exists editorial_highlights jsonb not null default '[]'::jsonb,
  add column if not exists visit_details jsonb not null default '{}'::jsonb,
  add column if not exists amenities jsonb not null default '[]'::jsonb,
  add column if not exists faqs jsonb not null default '[]'::jsonb,
  add column if not exists gallery_images jsonb not null default '[]'::jsonb,
  add column if not exists editorial_source_urls text[] not null default '{}',
  add column if not exists editorial_verified_at timestamptz;

do $$
begin
  if not exists (select 1 from pg_constraint where conname = 'casinos_profile_version_check') then
    alter table public.casinos add constraint casinos_profile_version_check check (profile_version between 1 and 3);
  end if;
  if not exists (select 1 from pg_constraint where conname = 'casinos_key_facts_array_check') then
    alter table public.casinos add constraint casinos_key_facts_array_check check (jsonb_typeof(key_facts) = 'array');
  end if;
  if not exists (select 1 from pg_constraint where conname = 'casinos_editorial_highlights_array_check') then
    alter table public.casinos add constraint casinos_editorial_highlights_array_check check (jsonb_typeof(editorial_highlights) = 'array');
  end if;
  if not exists (select 1 from pg_constraint where conname = 'casinos_visit_details_object_check') then
    alter table public.casinos add constraint casinos_visit_details_object_check check (jsonb_typeof(visit_details) = 'object');
  end if;
  if not exists (select 1 from pg_constraint where conname = 'casinos_amenities_array_check') then
    alter table public.casinos add constraint casinos_amenities_array_check check (jsonb_typeof(amenities) = 'array');
  end if;
  if not exists (select 1 from pg_constraint where conname = 'casinos_faqs_array_check') then
    alter table public.casinos add constraint casinos_faqs_array_check check (jsonb_typeof(faqs) = 'array');
  end if;
  if not exists (select 1 from pg_constraint where conname = 'casinos_gallery_images_array_check') then
    alter table public.casinos add constraint casinos_gallery_images_array_check check (jsonb_typeof(gallery_images) = 'array');
  end if;
end
$$;

comment on column public.casinos.profile_version is 'Version of the public editorial profile. Version 2 uses only source-backed content and facts.';
comment on column public.casinos.key_facts is 'Array of source-backed label/value facts rendered instead of legacy listing counts.';
comment on column public.casinos.editorial_highlights is 'Array of concise title/text editorial highlights.';
comment on column public.casinos.visit_details is 'Object containing hours, access, getting_there and parking guidance.';
comment on column public.casinos.amenities is 'Array of hotel, dining, entertainment and other resort amenities.';
comment on column public.casinos.faqs is 'Array of public question/answer objects used for page content and FAQ structured data.';
comment on column public.casinos.gallery_images is 'Array of licensed or owned gallery images with attribution metadata.';
comment on column public.casinos.editorial_source_urls is 'Official sources used to rewrite and verify the public guide.';

with profiles as (
  select *
  from jsonb_to_recordset($profiles$
  [
    {
      "id": 1,
      "expected_name": "Casino de Montréal",
      "short_description": "Island landmark and all-round entertainment venue",
      "summary": "Casino de Montréal occupies the former French and Québec pavilions from Expo 67 on Île Notre-Dame. The Loto-Québec venue combines a broad gaming offer with restaurants, bars, immersive attractions and a cabaret close to central Montréal.",
      "editorial_title": "A practical guide to Casino de Montréal",
      "editorial_paragraphs": [
        "Casino de Montréal is unusual before a single game is played: the complex reuses two striking Expo 67 buildings on Île Notre-Dame. The layered, circular interiors create a venue that feels more like a large entertainment campus than a conventional downtown gaming floor, with views and public spaces spread across several levels.",
        "The casino is operated by Loto-Québec and serves both regular players and visitors looking for an evening out. Its official programme combines classic tables, electronic games, poker, slot machines and The Zone, an interactive area designed around lower minimum wagers and a more social format. Game schedules vary, so poker and individual tables should be checked separately from the main building hours.",
        "Food and entertainment are a significant part of the visit. Le Montréal and Pavillon 67 anchor the dining offer, while bars, seasonal terraces, the Cabaret du Casino and the ARcade experience make it possible to plan a visit without treating gaming as the only attraction. There is no casino hotel attached to the building, so accommodation is best arranged in central Montréal or near a convenient metro connection."
      ],
      "games_description": "The official game directory lists baccarat, blackjack, roulette, craps, Sic Bo, keno, poker variants, a dedicated poker room, slot machines, electronic games and The Zone. Availability, table minimums and tournament calendars change; consult the live game pages before making a trip for one specific format.",
      "fun_facts": "The venue occupies the former French and Québec pavilions built for Expo 67, giving Casino de Montréal one of the most distinctive architectural settings in the Canadian casino market.",
      "seo_title": "Casino de Montréal: Games, Hours, Dining & Guide",
      "seo_description": "Plan a visit to Casino de Montréal with verified hours, games, restaurants, access advice, map, official links and practical FAQs.",
      "official_website_url": "https://casinos.lotoquebec.com/en/montreal/home",
      "street_address": "1 Avenue du Casino, Montréal, QC H3C 4W7, Canada",
      "phone": "+1 514-392-2746",
      "verified_opened_on": "1993-10-09",
      "verified_operator_name": "Loto-Québec",
      "hotel_name": null,
      "always_open": false,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": false,
      "has_bingo": false,
      "key_facts": [
        {"label":"Opened","value":"1993"},
        {"label":"Minimum age","value":"18+"},
        {"label":"Setting","value":"Île Notre-Dame"},
        {"label":"Hotel on site","value":"No"}
      ],
      "editorial_highlights": [
        {"title":"Expo 67 architecture","text":"The casino reuses the former French and Québec pavilions, creating a multi-level venue unlike a standard gaming hall."},
        {"title":"A broad game mix","text":"Tables, poker, slots, electronic games and the social-format Zone serve different types of visit."},
        {"title":"More than gaming","text":"Restaurants, bars, seasonal spaces, a cabaret and the ARcade experience extend the programme."}
      ],
      "visit_details": {
        "hours":"The official schedule currently lists Monday to Thursday from 9 a.m. to 5 a.m. the following day, then continuous opening from Friday 9 a.m. until Monday 5 a.m. Table, poker and restaurant hours differ.",
        "access":"Casino gaming is restricted to guests aged 18 or over. Bring valid government-issued photo ID and review the venue rules before arrival.",
        "getting_there":"The casino is on Île Notre-Dame. Public transport via Jean-Drapeau station and the connecting bus is practical from downtown; taxis and ride services can use the casino entrance.",
        "parking":"On-site parking is available. Check current pricing, event restrictions and accessible spaces on the official plan-your-visit page."
      },
      "amenities": [
        {"category":"dining","name":"Le Montréal and Pavillon 67","description":"The main restaurant choices range from a full-service evening restaurant to a broad buffet-style offer; opening days and reservations vary."},
        {"category":"entertainment","name":"Cabaret, ARcade and The Zone","description":"Shows, immersive play and a social electronic-gaming area give non-traditional options beyond the main tables."},
        {"category":"hotel","name":"Stay in Montréal","description":"There is no attached casino hotel. Central Montréal offers the widest hotel choice and straightforward access by metro, bus or taxi."}
      ],
      "faqs": [
        {"question":"What is the minimum age at Casino de Montréal?","answer":"Guests must be at least 18 to enter the gaming areas. Carry valid government-issued photo identification."},
        {"question":"Is Casino de Montréal open 24 hours?","answer":"It is not continuously open every day. The current official schedule has reduced overnight closure Monday to Thursday and continuous opening from Friday morning to early Monday; verify the latest schedule before travelling."},
        {"question":"Does Casino de Montréal have a hotel?","answer":"No hotel is attached to the casino. Visitors normally stay in central Montréal and travel to Île Notre-Dame."},
        {"question":"What games are available?","answer":"The official directory includes slots, electronic games, roulette, blackjack, baccarat, craps, Sic Bo, keno and several poker formats. Live availability and poker schedules can change."}
      ],
      "editorial_source_urls": [
        "https://casinos.lotoquebec.com/en/montreal/home",
        "https://casinos.lotoquebec.com/en/montreal/plan-your-visit",
        "https://casinos.lotoquebec.com/en/montreal/games/all-games"
      ]
    },
    {
      "id": 242,
      "expected_name": "The Venetian Macao",
      "short_description": "All-suite Cotai resort with canals, arena and gaming",
      "summary": "The Venetian Macao is a large integrated resort on the Cotai Strip, pairing an all-suite hotel and extensive casino with indoor canals, shopping, restaurants and a major arena. It works as a self-contained destination rather than a stand-alone gaming hall.",
      "editorial_title": "Inside The Venetian Macao resort",
      "editorial_paragraphs": [
        "The Venetian Macao translates the scale and visual language of its Las Vegas counterpart into Cotai. Guests move between an all-suite hotel, the Shoppes at Venetian, canal-side interiors and a substantial entertainment programme without leaving the complex. The result is best approached as a resort day or overnight stay rather than a quick casino stop.",
        "Gaming is spread across a large main floor with live tables, slot machines and electronic formats. The mix is shaped by the Macao market, where baccarat is central, while game availability and minimum stakes can change by area and time. Premium rooms have separate access conditions, so the public casino floor is the relevant reference for most first-time visitors.",
        "The non-gaming offer is a major strength. The resort promotes a wide restaurant collection, indoor retail streets and canal attractions, while the Venetian Arena hosts concerts and sporting events. The official resort calendar is essential because dining hours, performances and event-day circulation can affect how the property feels."
      ],
      "games_description": "The public casino combines live table gaming, slot machines and electronic games. Baccarat is prominent, alongside other table formats offered according to current floor schedules. Premium gaming areas and individual tables may apply separate entry or stake requirements.",
      "fun_facts": "The property is designed as an integrated Cotai destination: hotel suites, a canal-themed shopping complex, dining and a large arena sit around the casino rather than functioning as separate venues.",
      "seo_title": "The Venetian Macao: Casino, Hotel & Visit Guide",
      "seo_description": "Explore The Venetian Macao with sourced casino details, hotel and dining guidance, access advice, map, official links and FAQs.",
      "official_website_url": "https://www.venetianmacao.com/",
      "street_address": "Estrada da Baía de Nossa Senhora da Esperança, Cotai, Macao",
      "phone": "+853 2882 8888",
      "verified_opened_on": "2007-08-28",
      "verified_operator_name": "Sands China Ltd.",
      "hotel_name": "The Venetian Macao",
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": false,
      "has_bingo": false,
      "key_facts": [
        {"label":"Opened","value":"2007"},
        {"label":"Minimum age","value":"21+"},
        {"label":"Resort area","value":"Cotai Strip"},
        {"label":"Hotel","value":"All-suite resort"}
      ],
      "editorial_highlights": [
        {"title":"Integrated scale","text":"Casino, all-suite accommodation, retail streets, canals, restaurants and an arena form one large Cotai destination."},
        {"title":"Macao gaming mix","text":"The floor centres on live tables, slots and electronic games, with baccarat particularly visible."},
        {"title":"Easy full-day visit","text":"Dining, shopping and entertainment make it practical for mixed groups whose plans are not exclusively about gaming."}
      ],
      "visit_details": {
        "hours":"The main resort and casino operate daily, with many casino areas running around the clock. Restaurants, attractions, the arena and individual gaming zones use their own schedules.",
        "access":"Macao casino entry is restricted to people aged 21 or over. International visitors should carry a passport; premium rooms may have additional conditions.",
        "getting_there":"The resort is in Cotai and is served by taxis, public buses, the light rail network and selected resort shuttle services. Allow extra time on major event dates.",
        "parking":"On-site parking is available within the resort complex. Use the official transport information for current access, validation and event-day arrangements."
      },
      "amenities": [
        {"category":"hotel","name":"The Venetian Macao suites","description":"The attached all-suite hotel makes the property suitable for an overnight resort stay with direct indoor access to the complex."},
        {"category":"dining","name":"Large restaurant collection","description":"The official dining directory covers Chinese regional cooking, international restaurants, casual meals and late-night options across the resort."},
        {"category":"entertainment","name":"Canals, shopping and arena events","description":"Indoor gondola attractions, the Shoppes and the Venetian Arena provide a substantial programme beyond the casino floor."}
      ],
      "faqs": [
        {"question":"How old must you be to enter The Venetian Macao casino?","answer":"Casino entry in Macao is limited to guests aged 21 or over. Carry valid identification, preferably a passport for international visitors."},
        {"question":"Is there a hotel attached to the casino?","answer":"Yes. The Venetian Macao is an all-suite hotel integrated directly with the casino, shops, restaurants and entertainment venues."},
        {"question":"What games are offered?","answer":"The public floor offers live table games, slot machines and electronic gaming. The precise mix and minimum stakes change by zone and operating period."},
        {"question":"Can non-gamblers enjoy the resort?","answer":"Yes. The canals, shopping areas, restaurants and arena make the property useful for visitors who do not plan to enter the casino."}
      ],
      "editorial_source_urls": [
        "https://www.venetianmacao.com/",
        "https://www.venetianmacao.com/restaurants.html"
      ]
    },
    {
      "id": 2143,
      "expected_name": "Wynn Las Vegas",
      "short_description": "Luxury Strip resort focused on design, dining and gaming",
      "summary": "Wynn Las Vegas and the adjoining Encore form a polished resort at the north end of the Strip. The destination combines casino gaming with acclaimed restaurants, luxury rooms, retail, nightlife, theatre and landscaped resort spaces.",
      "editorial_title": "What to expect at Wynn Las Vegas",
      "editorial_paragraphs": [
        "Wynn Las Vegas is built around a quieter, design-led interpretation of the Strip resort. The casino is central but does not dominate every part of the property: hotel corridors, gardens, shopping and restaurants create distinct zones, making the complex easier to use for a stay, dinner or show without remaining on the gaming floor.",
        "The gaming offer covers slot machines, live table games, poker and a sportsbook, with higher-limit spaces available for experienced players. As at other Las Vegas resorts, the main casino runs continuously while poker, race and sports, cage services and individual promotions follow separate schedules.",
        "Dining is one of the resort's strongest reasons to visit. Wynn currently highlights restaurants such as SW Steakhouse, Mizumi, Delilah and Casa Playa, supported by casual dining, bars and round-the-clock in-room service. Entertainment, nightlife, spa facilities and shopping complete a property that works best when planned as a broader resort experience."
      ],
      "games_description": "The casino offers slots, table games, poker and race-and-sports betting, with standard and higher-limit environments. Specific poker games, table minimums and sportsbook hours should be confirmed on the official casino pages.",
      "fun_facts": "Wynn and Encore operate as a connected resort, so visitors can combine two hotel experiences, a broad restaurant portfolio and multiple entertainment spaces without leaving the complex.",
      "seo_title": "Wynn Las Vegas Casino, Hotel & Dining Guide",
      "seo_description": "Plan Wynn Las Vegas with verified casino, hotel, dining, access and location information plus official sources and FAQs.",
      "official_website_url": "https://www.wynnlasvegas.com/",
      "street_address": "3131 Las Vegas Boulevard South, Las Vegas, NV 89109, United States",
      "phone": "+1 702-770-7000",
      "verified_opened_on": "2005-04-28",
      "verified_operator_name": "Wynn Resorts",
      "hotel_name": "Wynn Las Vegas and Encore",
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": true,
      "has_bingo": false,
      "key_facts": [
        {"label":"Opened","value":"2005"},
        {"label":"Minimum age","value":"21+"},
        {"label":"Casino hours","value":"24 hours"},
        {"label":"Connected resort","value":"Wynn + Encore"}
      ],
      "editorial_highlights": [
        {"title":"Refined resort layout","text":"Gaming, gardens, hotel areas, shops and restaurants are organised into distinct spaces rather than one continuous casino floor."},
        {"title":"Complete casino offer","text":"Slots, tables, poker and sports betting cover the principal Las Vegas gaming formats."},
        {"title":"Destination dining","text":"Fine dining, casual restaurants, lounges and all-day service make food a core part of the Wynn experience."}
      ],
      "visit_details": {
        "hours":"The main casino is open 24 hours. Poker, sportsbook, restaurants, shows and individual services publish separate operating schedules.",
        "access":"Casino gaming and alcohol service are restricted to guests aged 21 or over. Bring government-issued photo ID; under-21 visitors may pass through designated resort routes but cannot linger in gaming areas.",
        "getting_there":"Wynn is on Las Vegas Boulevard at the north end of the central Strip, opposite Fashion Show. Taxis, ride services and local buses serve the resort entrances.",
        "parking":"Self-parking and valet options are available. Rates and complimentary-parking rules can change, so check the official resort information before arrival."
      },
      "amenities": [
        {"category":"hotel","name":"Wynn and Encore rooms","description":"Two connected luxury hotel towers provide direct access to the resort's gaming, dining, retail and entertainment spaces."},
        {"category":"dining","name":"Fine and casual dining","description":"The current portfolio ranges from SW Steakhouse and Mizumi to casual cafés, lounges and 24-hour in-room dining."},
        {"category":"entertainment","name":"Shows, nightlife and resort experiences","description":"Theatre, nightlife, spa, pools, shopping and landscaped attractions support a full resort itinerary."}
      ],
      "faqs": [
        {"question":"Is Wynn Las Vegas casino open 24 hours?","answer":"The main casino operates 24 hours, while poker, sportsbook, restaurants and entertainment venues use separate schedules."},
        {"question":"What is the minimum age to gamble at Wynn?","answer":"Guests must be 21 or older to gamble or consume alcohol. Valid government-issued photo ID may be requested."},
        {"question":"Are Wynn and Encore connected?","answer":"Yes. Wynn Las Vegas and Encore form one connected resort with shared access to gaming, restaurants, shops and entertainment."},
        {"question":"What games can visitors expect?","answer":"The casino offers slots, live table games, poker and sports betting. Current tables, poker formats and minimums should be checked directly with the resort."}
      ],
      "editorial_source_urls": [
        "https://www.wynnlasvegas.com/",
        "https://www.wynnlasvegas.com/dining",
        "https://www.wynnlasvegas.com/casino"
      ]
    },
    {
      "id": 3088,
      "expected_name": "Wynn Palace Cotai",
      "short_description": "Art-led Cotai resort beside the Performance Lake",
      "summary": "Wynn Palace is a luxury integrated resort in Cotai built around a large casino, hotel, restaurants, art and the choreographed Performance Lake. Its SkyCab arrival and floral interiors make the resort experience as prominent as the gaming floor.",
      "editorial_title": "A visitor's guide to Wynn Palace Cotai",
      "editorial_paragraphs": [
        "Wynn Palace uses the Performance Lake as its front door. The SkyCab crosses above the water toward a resort filled with large floral displays and a curated art collection, establishing a strong sense of place before guests reach the casino or hotel reception.",
        "The casino provides live table gaming and electronic or slot formats within the wider Cotai resort. Baccarat is an important part of the Macao market, but table availability, public-floor minimums and premium-room access change over time. Visitors should use the official casino information or ask the resort directly for a specific game.",
        "Accommodation and dining are integral to the property. Wynn Palace promotes a large room and suite inventory, a substantial spa, luxury retail and restaurants ranging from Chef Tam's Seasons and SW Steakhouse to casual cafés and a newer gourmet pavilion. The lake show, SkyCab and dining make the resort suitable for mixed groups."
      ],
      "games_description": "The casino includes live table gaming, slot machines and electronic games, with baccarat central to the Macao offer. Public and premium areas may have different game selections, minimum stakes and access rules.",
      "fun_facts": "The resort's signature Performance Lake combines water, music and light, while the SkyCab creates an aerial approach to the hotel entrance.",
      "seo_title": "Wynn Palace Cotai Casino & Resort Guide",
      "seo_description": "Explore Wynn Palace Cotai with sourced casino, hotel, dining, access, transport, map and FAQ information.",
      "official_website_url": "https://www.wynnpalace.com/",
      "street_address": "Avenida da Nave Desportiva, Cotai, Macao",
      "phone": "+853 8889 8889",
      "verified_opened_on": "2016-08-22",
      "verified_operator_name": "Wynn Macau, Limited",
      "hotel_name": "Wynn Palace",
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": false,
      "has_bingo": false,
      "key_facts": [
        {"label":"Opened","value":"2016"},
        {"label":"Minimum age","value":"21+"},
        {"label":"Location","value":"Cotai"},
        {"label":"Signature attraction","value":"Performance Lake"}
      ],
      "editorial_highlights": [
        {"title":"SkyCab arrival","text":"A cable-car ride over the Performance Lake gives Wynn Palace one of Cotai's most recognisable resort entrances."},
        {"title":"Art and floral design","text":"Large installations, floral displays and a curated collection shape the public spaces around the casino."},
        {"title":"Strong dining programme","text":"Fine dining, casual venues and the gourmet pavilion broaden the property beyond gaming."}
      ],
      "visit_details": {
        "hours":"The resort and casino operate daily, with the main gaming floor generally available around the clock. Restaurants, SkyCab service and shows have separate hours and weather conditions may affect outdoor attractions.",
        "access":"Casino areas are restricted to guests aged 21 or over. Carry a passport or recognised photo ID; premium areas can apply additional entry conditions.",
        "getting_there":"Wynn Palace is on Avenida da Nave Desportiva in Cotai. Taxis, public buses, light rail and selected resort transport connect the property with other Cotai resorts and border terminals.",
        "parking":"The resort provides on-site parking. Confirm current guest validation and public-parking conditions with the property."
      },
      "amenities": [
        {"category":"hotel","name":"Wynn Palace rooms and suites","description":"The attached luxury hotel gives direct indoor access to gaming, dining, spa, retail and event spaces."},
        {"category":"dining","name":"Chef-led restaurants and casual dining","description":"The portfolio includes Chef Tam's Seasons, SW Steakhouse, Mizumi, cafés and a broad gourmet pavilion."},
        {"category":"entertainment","name":"Performance Lake and SkyCab","description":"The choreographed lake, aerial cable car, art and floral displays are signature non-gaming attractions."}
      ],
      "faqs": [
        {"question":"What is the minimum age at Wynn Palace casino?","answer":"Casino entry in Macao is restricted to people aged 21 or over. International guests should carry a passport."},
        {"question":"Is Wynn Palace a hotel as well as a casino?","answer":"Yes. Wynn Palace is an integrated resort with hotel rooms and suites, casino gaming, restaurants, shops, spa and entertainment."},
        {"question":"Can visitors ride the SkyCab without gambling?","answer":"The SkyCab and Performance Lake are resort attractions, but operating hours and weather restrictions can change. Check the official entertainment page before visiting."},
        {"question":"What games are available?","answer":"Visitors can expect live table gaming, slots and electronic formats. The current selection and minimum stakes vary across public and premium areas."}
      ],
      "editorial_source_urls": [
        "https://www.wynnpalace.com/",
        "https://www.wynnpalace.com/en/restaurants-n-bars/fine-dining/mizumi",
        "https://www.wynnpalace.com/en/entertainment/skycab-cable-car"
      ]
    },
    {
      "id": 3287,
      "expected_name": "Foxwoods Resort Casino",
      "short_description": "Large Connecticut resort with gaming, hotels and shows",
      "summary": "Foxwoods Resort Casino is a large Mashantucket destination operated by the Mashantucket Pequot Tribal Nation. Multiple casino areas, hotels, restaurants, theatres, shopping, bingo, poker and a sportsbook make it a full resort rather than a single gaming room.",
      "editorial_title": "How to plan a Foxwoods visit",
      "editorial_paragraphs": [
        "Foxwoods is best understood as a connected resort campus. Different casino areas sit alongside the Grand Pequot Tower, Great Cedar Hotel and Fox Tower, with long indoor routes linking gaming, dining, retail and entertainment. First-time visitors should use the property map and choose parking based on their main destination.",
        "The official gaming guide lists slot machines, live table games, poker, bingo, keno and sportsbook action. Because these products sit in different parts of the resort and may follow different schedules, visitors coming for bingo, poker or a particular sportsbook event should confirm the exact room and start time in advance.",
        "Foxwoods has broadened its dining and entertainment offer with celebrity-chef restaurants, casual venues, late-night options, theatres and activities. That range makes it suitable for a weekend stay or a mixed group, but it also means reservations are useful on concert nights and during major promotions."
      ],
      "games_description": "Foxwoods officially offers slots, table games, poker, bingo, keno and sportsbook betting across several casino areas. Room locations, tournament schedules and age rules can differ, particularly for bingo and non-gaming facilities.",
      "fun_facts": "The resort contains several distinct casino and hotel zones connected indoors, so choosing the right garage or entrance can materially shorten the walk to a specific activity.",
      "seo_title": "Foxwoods Resort Casino: Games, Hotels & Guide",
      "seo_description": "Plan Foxwoods Resort Casino with verified games, hotels, restaurants, access, parking, map, sources and practical FAQs.",
      "official_website_url": "https://foxwoods.com/",
      "street_address": "350 Trolley Line Boulevard, Mashantucket, CT 06338, United States",
      "phone": "+1 860-312-3000",
      "verified_opened_on": "1992-02-15",
      "verified_operator_name": "Mashantucket Pequot Tribal Nation",
      "hotel_name": "Grand Pequot Tower, Great Cedar Hotel and Fox Tower",
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": true,
      "has_bingo": true,
      "key_facts": [
        {"label":"Casino opened","value":"1992"},
        {"label":"Casino gaming age","value":"21+"},
        {"label":"Resort type","value":"Multi-casino campus"},
        {"label":"Hotels","value":"Three main towers"}
      ],
      "editorial_highlights": [
        {"title":"Several casino zones","text":"Gaming is distributed across a connected resort, so maps and entrance planning matter more than at a compact casino."},
        {"title":"Unusually broad game choice","text":"Slots, tables, poker, bingo, keno and sportsbook action all appear in the official gaming directory."},
        {"title":"Weekend destination","text":"Hotels, restaurants, theatres, shops and activities support an overnight stay without leaving the property."}
      ],
      "visit_details": {
        "hours":"The resort's main gaming areas operate around the clock, while bingo, poker, sportsbook counters, restaurants, shops and entertainment venues publish individual hours.",
        "access":"Casino gaming is generally restricted to guests aged 21 or over. Bingo and non-gaming attractions may use different rules; valid photo ID is required where age verification applies.",
        "getting_there":"Foxwoods is in Mashantucket, Connecticut and is primarily reached by car, coach or regional shuttle. Consult the official directions page for transport options from nearby cities and airports.",
        "parking":"Multiple garages and valet areas serve different resort zones. Choose the garage closest to the casino, hotel, theatre or restaurant on your itinerary."
      },
      "amenities": [
        {"category":"hotel","name":"Three main hotel choices","description":"Grand Pequot Tower, Great Cedar Hotel and Fox Tower offer different locations and styles within the connected resort."},
        {"category":"dining","name":"Celebrity-chef to late-night dining","description":"The official directory ranges from headline restaurants to casual food, sports-bar dining and late-night choices."},
        {"category":"entertainment","name":"Shows, shopping and activities","description":"Theatres, retail, bowling and seasonal experiences make it possible to build a non-gaming programme around the visit."}
      ],
      "faqs": [
        {"question":"Is Foxwoods open 24 hours?","answer":"The main casino areas operate around the clock, but poker, bingo, restaurants, shops and entertainment venues have separate schedules."},
        {"question":"How old must you be to gamble at Foxwoods?","answer":"Casino gaming is generally limited to guests aged 21 or over. Some non-casino activities and bingo policies can differ, so check the specific venue."},
        {"question":"Does Foxwoods have hotels?","answer":"Yes. The resort includes Grand Pequot Tower, Great Cedar Hotel and Fox Tower, each connected to different parts of the complex."},
        {"question":"What games does Foxwoods offer?","answer":"The official guide lists slots, table games, poker, bingo, keno and sportsbook action. Schedules and locations vary by casino zone."}
      ],
      "editorial_source_urls": [
        "https://foxwoods.com/",
        "https://foxwoods.com/play",
        "https://foxwoods.com/dining"
      ]
    },
    {
      "id": 3524,
      "expected_name": "Bellagio Hotel and Casino",
      "short_description": "Fountains, fine dining and classic Las Vegas gaming",
      "summary": "Bellagio is a central Las Vegas Strip resort known for its lake and fountain show, conservatory, fine dining, poker and elegant casino floor. The hotel, spa, restaurants and theatre make it one of the Strip's most complete destination properties.",
      "editorial_title": "Bellagio beyond the fountains",
      "editorial_paragraphs": [
        "Bellagio's public identity starts with the lake, but the resort experience continues through the botanical Conservatory, gallery spaces, restaurants and the theatre built for O by Cirque du Soleil. The casino sits at the centre of this programme and is easy to reach from the main lobby and Strip entrances.",
        "The gaming floor combines a large slot selection, live table games, poker and a sportsbook. Bellagio's official casino pages distinguish between the main floor, poker room and sports betting, which is useful because each area has its own services and hours even though the main casino remains open around the clock.",
        "For an overnight stay, Bellagio provides direct access to rooms, pools, spa and multiple dining formats, from fine restaurants to faster options. Fountain-view demand and show schedules can make the property busy, so advance reservations are sensible for headline restaurants, O and weekend stays."
      ],
      "games_description": "Bellagio offers slots and video poker, live table games, a dedicated poker room and a sportsbook. The resort's official pages provide the current poker schedule, table information and sports-betting services.",
      "fun_facts": "The Fountains of Bellagio sit on an artificial lake directly in front of the resort, while the indoor Conservatory changes its large botanical display seasonally.",
      "seo_title": "Bellagio Las Vegas Casino, Hotel & Visit Guide",
      "seo_description": "Explore Bellagio Las Vegas with sourced casino games, hotel, dining, access, parking, map, official links and FAQs.",
      "official_website_url": "https://bellagio.mgmresorts.com/en/casino.html",
      "street_address": "3600 South Las Vegas Boulevard, Las Vegas, NV 89109, United States",
      "phone": "+1 702-693-7111",
      "verified_opened_on": "1998-10-15",
      "verified_operator_name": "MGM Resorts International",
      "hotel_name": "Bellagio Las Vegas",
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": true,
      "has_bingo": false,
      "key_facts": [
        {"label":"Opened","value":"1998"},
        {"label":"Minimum age","value":"21+"},
        {"label":"Casino hours","value":"24 hours"},
        {"label":"Signature attraction","value":"Fountains of Bellagio"}
      ],
      "editorial_highlights": [
        {"title":"Iconic setting","text":"The lake, fountains and central Strip position make Bellagio one of the easiest Las Vegas resorts to recognise."},
        {"title":"Serious poker offer","text":"A dedicated poker room, table-side services and a separate schedule complement the main casino floor."},
        {"title":"Full resort programme","text":"Hotel rooms, O, the Conservatory, restaurants, pools and spa support a complete stay."}
      ],
      "visit_details": {
        "hours":"The main casino is open 24 hours. Poker, sportsbook, restaurants, shows and attractions maintain separate schedules.",
        "access":"Gaming and alcohol service are for guests aged 21 or over. Under-21 visitors may use designated routes through the resort but cannot stop in gaming areas; bring valid photo ID.",
        "getting_there":"Bellagio is at 3600 South Las Vegas Boulevard, near the centre of the Strip. It is served by taxis, ride services, buses and pedestrian links from neighbouring resorts.",
        "parking":"Self-parking and valet are available. Fees, MGM Rewards benefits and event conditions can change, so verify current parking rules online."
      },
      "amenities": [
        {"category":"hotel","name":"Bellagio hotel and spa","description":"Rooms, suites, pools, spa and salon are integrated with the casino and central resort facilities."},
        {"category":"dining","name":"Fine dining and casual choices","description":"The resort combines destination restaurants, lounges, cafés and in-room service; reservations are recommended for popular venues."},
        {"category":"entertainment","name":"Fountains, Conservatory and O","description":"Free public attractions and a major Cirque du Soleil production give Bellagio a strong non-gaming identity."}
      ],
      "faqs": [
        {"question":"Is Bellagio casino open 24 hours?","answer":"Yes, the main casino operates 24 hours. Poker, sportsbook, restaurants and shows have their own schedules."},
        {"question":"How old must you be to gamble at Bellagio?","answer":"You must be 21 or older to gamble. Carry valid government-issued photo identification."},
        {"question":"Does Bellagio have a poker room?","answer":"Yes. Bellagio operates a dedicated poker room with its own contact details, game schedule and player services."},
        {"question":"Are the Bellagio fountains free to watch?","answer":"The outdoor fountain show is a public attraction viewed from the Strip and resort frontage. Performance timing can change because of weather or special events."}
      ],
      "editorial_source_urls": [
        "https://bellagio.mgmresorts.com/en/casino.html",
        "https://bellagio.mgmresorts.com/en/contact-us.html",
        "https://bellagio.mgmresorts.com/en/casino/poker.html"
      ]
    },
    {
      "id": 3547,
      "expected_name": "Caesars Palace Casino",
      "short_description": "Historic Strip resort with casino, shows and dining",
      "summary": "Caesars Palace is a long-established central Strip resort combining a large casino with hotel towers, The Colosseum, destination restaurants, pools, spa and direct access to The Forum Shops. Its scale supports both a quick gaming visit and a full Las Vegas stay.",
      "editorial_title": "Planning a visit to Caesars Palace",
      "editorial_paragraphs": [
        "Caesars Palace has expanded repeatedly since opening in 1966, and the result is a resort made up of several hotel towers and distinct public zones. The casino, lobby, Colosseum, Forum Shops and restaurant areas can involve substantial walking, so checking a property map before meeting friends or booking dinner is worthwhile.",
        "The casino's official offer includes slot machines, live tables, poker, higher-limit spaces and sports betting. The main floor runs continuously, but poker events, the sportsbook desk and individual tables follow their own schedules. Visitors looking for one specific game should confirm current availability instead of relying on historical machine or table counts.",
        "Caesars is especially strong for dining and headline entertainment. The restaurant collection spans Bacchanal Buffet, Nobu, Hell's Kitchen and other celebrity-led venues, while The Colosseum anchors the show programme. The Forum Shops and the resort's pool complex make it easy to build a broader day around the casino."
      ],
      "games_description": "Caesars Palace offers slots, table games, poker, high-limit gaming and sports betting. Live games, poker schedules and wagering services should be checked on the official casino page for the planned date.",
      "fun_facts": "The resort has grown through multiple hotel towers and public spaces since 1966, creating a large internal network between the casino, The Colosseum and The Forum Shops.",
      "seo_title": "Caesars Palace Casino, Hotel & Dining Guide",
      "seo_description": "Plan Caesars Palace with verified casino games, hotel, restaurants, access, parking, map, official links and useful FAQs.",
      "official_website_url": "https://www.caesars.com/caesars-palace/casino",
      "street_address": "3570 South Las Vegas Boulevard, Las Vegas, NV 89109, United States",
      "phone": "+1 866-227-5938",
      "verified_opened_on": "1966-08-05",
      "verified_operator_name": "Caesars Entertainment",
      "hotel_name": "Caesars Palace",
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": true,
      "has_bingo": false,
      "key_facts": [
        {"label":"Opened","value":"1966"},
        {"label":"Minimum age","value":"21+"},
        {"label":"Casino hours","value":"24 hours"},
        {"label":"Entertainment venue","value":"The Colosseum"}
      ],
      "editorial_highlights": [
        {"title":"A Las Vegas institution","text":"More than five decades of expansion have made Caesars one of the Strip's best-known resort complexes."},
        {"title":"Broad casino floor","text":"Slots, tables, poker, premium spaces and sports betting cover the principal gaming formats."},
        {"title":"Dining and shows","text":"Headline restaurants and The Colosseum make reservations and event timing central to a good visit."}
      ],
      "visit_details": {
        "hours":"The main casino operates 24 hours. Poker, sportsbook services, restaurants, shops and shows have separate schedules.",
        "access":"Guests must be 21 or older to gamble or drink alcohol. Under-21 visitors can access hotels, restaurants, shops and shows via designated paths but may not remain in gaming areas.",
        "getting_there":"Caesars Palace is on the central Strip between Bellagio and The Mirage/Hard Rock area, with taxi, ride-service and bus access plus pedestrian links to neighbouring properties.",
        "parking":"A large self-parking garage and valet services are available. Current fees and Caesars Rewards benefits should be confirmed before arrival."
      },
      "amenities": [
        {"category":"hotel","name":"Multiple Caesars Palace towers","description":"Several hotel towers provide different room categories and walking distances within the large resort."},
        {"category":"dining","name":"Bacchanal, Nobu and celebrity dining","description":"A wide restaurant portfolio ranges from a major buffet to fine dining, casual venues and late-night food."},
        {"category":"entertainment","name":"The Colosseum and Forum Shops","description":"Concerts, retail, pools and spa broaden the itinerary beyond the casino floor."}
      ],
      "faqs": [
        {"question":"Is Caesars Palace casino open 24 hours?","answer":"The main casino is open 24 hours, while poker, sportsbook, restaurants, shops and entertainment venues have individual hours."},
        {"question":"What age is required to gamble?","answer":"Casino gaming is restricted to guests aged 21 or over. Valid government-issued photo ID may be required."},
        {"question":"What games are available at Caesars Palace?","answer":"The official casino offer includes slots, live tables, poker, higher-limit gaming and sports betting. Availability changes by date and time."},
        {"question":"Are The Forum Shops connected to Caesars Palace?","answer":"Yes. The Forum Shops are directly connected to the resort, although the complex is large and walking times can be longer than expected."}
      ],
      "editorial_source_urls": [
        "https://www.caesars.com/caesars-palace/casino",
        "https://www.caesars.com/caesars-palace/restaurants"
      ]
    },
    {
      "id": 3558,
      "expected_name": "The Venetian Las Vegas",
      "short_description": "All-suite Strip resort with canals and a large casino",
      "summary": "The Venetian Resort Las Vegas connects The Venetian and The Palazzo with a large casino, all-suite accommodation, Grand Canal Shoppes, restaurants, theatres and direct access toward Sphere. It is designed as a destination complex rather than a compact hotel casino.",
      "editorial_title": "The Venetian Las Vegas, explained",
      "editorial_paragraphs": [
        "The Venetian is one of the Strip's largest connected resorts. The Venetian and Palazzo hotel towers, casino areas, indoor canals and Grand Canal Shoppes form a network of public spaces that can take time to navigate. A property map is useful when coordinating a restaurant, theatre, poker room or hotel meeting point.",
        "Gaming covers slot machines, live tables, poker, sports betting and higher-limit areas. The main casino remains open around the clock, while the poker room, sportsbook desks and promotions use separate schedules. The Palazzo side provides an additional gaming environment within the same overall resort.",
        "The resort's official dining guide lists more than 40 restaurants, spanning celebrity chefs, fine dining, casual meals, food-hall formats and 24-hour options. Gondola rides, theatres, shopping, nightlife, spa facilities and access toward Sphere make it particularly suitable for groups with different priorities."
      ],
      "games_description": "The connected Venetian and Palazzo casino areas offer slots, live table games, poker, sports betting and higher-limit play. Specific poker games, table minimums and counter hours vary and should be checked directly.",
      "fun_facts": "The resort recreates Venetian canals and architecture indoors while connecting two all-suite hotel towers and one of the Strip's broadest restaurant collections.",
      "seo_title": "The Venetian Las Vegas Casino & Resort Guide",
      "seo_description": "Explore The Venetian Las Vegas with sourced casino, hotel, restaurant, access, parking, map and FAQ information.",
      "official_website_url": "https://www.venetianlasvegas.com/",
      "street_address": "3355 South Las Vegas Boulevard, Las Vegas, NV 89109, United States",
      "phone": "+1 702-414-1000",
      "verified_opened_on": "1999-05-03",
      "verified_operator_name": "The Venetian Resort Las Vegas",
      "hotel_name": "The Venetian and The Palazzo",
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": true,
      "has_bingo": false,
      "key_facts": [
        {"label":"Opened","value":"1999"},
        {"label":"Minimum age","value":"21+"},
        {"label":"Casino hours","value":"24 hours"},
        {"label":"Hotel format","value":"All suites"}
      ],
      "editorial_highlights": [
        {"title":"Two connected hotels","text":"The Venetian and Palazzo towers share casino, dining, retail and entertainment spaces."},
        {"title":"Complete gaming mix","text":"Slots, tables, poker, sportsbook and higher-limit rooms are spread across the connected floor."},
        {"title":"More than 40 restaurants","text":"The official dining guide covers fine dining, casual venues, food halls and late-night service."}
      ],
      "visit_details": {
        "hours":"The main casino areas operate 24 hours. Poker, sports betting counters, restaurants, shops, gondolas and shows publish individual schedules.",
        "access":"Casino gaming and alcohol service are restricted to guests aged 21 or over. Valid government-issued photo identification may be requested.",
        "getting_there":"The resort is on the east side of Las Vegas Boulevard near Sands Avenue and Sphere. Taxis, ride services, buses and pedestrian bridges serve the complex.",
        "parking":"Self-parking and valet facilities serve both Venetian and Palazzo areas. Use the destination closest to your hotel tower or restaurant and check current fees."
      },
      "amenities": [
        {"category":"hotel","name":"The Venetian and Palazzo suites","description":"Two connected all-suite towers offer different locations within the same large resort complex."},
        {"category":"dining","name":"More than 40 restaurants","description":"Fine dining, celebrity chefs, casual food, bars and 24-hour options create one of the Strip's broadest dining selections."},
        {"category":"entertainment","name":"Canals, theatres and Sphere access","description":"Gondola rides, shops, shows, spa, nightlife and nearby Sphere support a full-day or multi-night itinerary."}
      ],
      "faqs": [
        {"question":"Are The Venetian and The Palazzo connected?","answer":"Yes. The two hotel towers share a connected resort with casino areas, restaurants, shops and entertainment."},
        {"question":"Is the casino open 24 hours?","answer":"The main casino areas operate 24 hours. Poker, sportsbook counters, restaurants and attractions have separate schedules."},
        {"question":"What is the minimum gambling age?","answer":"Guests must be at least 21 to gamble or consume alcohol. Carry valid government-issued photo ID."},
        {"question":"Does the resort have many dining options?","answer":"Yes. The official directory lists more than 40 restaurants, from fine dining and celebrity chefs to casual and late-night venues."}
      ],
      "editorial_source_urls": [
        "https://www.venetianlasvegas.com/",
        "https://www.venetianlasvegas.com/dining/restaurants.html",
        "https://www.venetianlasvegas.com/casino.html"
      ]
    },
    {
      "id": 3571,
      "expected_name": "MGM Grand Las Vegas",
      "short_description": "High-energy Strip resort for gaming and major events",
      "summary": "MGM Grand is a large south-central Strip resort built around casino gaming, arena events, live shows, restaurants, nightlife and a substantial hotel. Its scale and event calendar create a lively atmosphere throughout the day and night.",
      "editorial_title": "A clear guide to MGM Grand Las Vegas",
      "editorial_paragraphs": [
        "MGM Grand is organised as an entertainment resort first: the casino connects hotel towers, the Grand Garden Arena, KÀ Theatre, restaurants, nightlife and pool areas. Event nights can materially increase foot traffic, so visitors attending a fight, concert or show should allow time to cross the property.",
        "The official casino pages cover thousands of slots and video poker games, live tables, a poker room and the BetMGM Sportsbook. The main floor operates continuously, while poker games, sportsbook cashier services and individual table types follow separate schedules. Historical counts should not be used as a guarantee of what is open on a given visit.",
        "Dining ranges from Tom Colicchio's Craftsteak and Morimoto to Italian-American food, quick service and bars. KÀ, the Grand Garden Arena, comedy, nightlife and the pool complex make the resort particularly suited to visitors who want a busy programme without travelling between properties."
      ],
      "games_description": "MGM Grand offers slots, video poker, electronic table games, live tables, poker and BetMGM sports betting. The official casino directory should be used for current game types, poker schedules and sportsbook hours.",
      "fun_facts": "The Grand Garden Arena gives the resort a major-event role on the Strip, so casino traffic can change sharply before and after concerts, boxing and other headline events.",
      "seo_title": "MGM Grand Las Vegas Casino, Hotel & Guide",
      "seo_description": "Plan MGM Grand Las Vegas with verified games, hotel, dining, shows, access, parking, map, sources and FAQs.",
      "official_website_url": "https://mgmgrand.mgmresorts.com/en/casino.html",
      "street_address": "3799 South Las Vegas Boulevard, Las Vegas, NV 89109, United States",
      "phone": "+1 877-880-0880",
      "verified_opened_on": "1993-12-18",
      "verified_operator_name": "MGM Resorts International",
      "hotel_name": "MGM Grand Las Vegas",
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": true,
      "has_bingo": false,
      "key_facts": [
        {"label":"Opened","value":"1993"},
        {"label":"Minimum age","value":"21+"},
        {"label":"Casino hours","value":"24 hours"},
        {"label":"Major venue","value":"Grand Garden Arena"}
      ],
      "editorial_highlights": [
        {"title":"Event-driven energy","text":"Arena events, KÀ, comedy and nightlife create a busier, louder resort than many luxury-focused competitors."},
        {"title":"All principal game types","text":"Slots, video poker, live and electronic tables, poker and sports betting appear in the official casino offer."},
        {"title":"Self-contained itinerary","text":"Hotel, dining, shows, nightlife and pools make it possible to stay within the property for an entire day."}
      ],
      "visit_details": {
        "hours":"The main casino operates 24 hours. Poker, sportsbook cashier, restaurants, shows and nightlife venues have separate schedules.",
        "access":"Guests must be 21 or older to gamble or consume alcohol. Under-21 visitors can use resort routes to reach hotel, dining and entertainment areas but cannot remain on the gaming floor.",
        "getting_there":"MGM Grand is at the Strip and Tropicana intersection. Taxis, ride services, buses and the Las Vegas Monorail serve the property.",
        "parking":"Self-parking and valet are available. Fees and MGM Rewards benefits vary; event nights can create delays at entrances and garages."
      },
      "amenities": [
        {"category":"hotel","name":"MGM Grand hotel and suites","description":"A substantial room inventory, plus premium suite products, supports both leisure and major-event stays."},
        {"category":"dining","name":"Celebrity restaurants and casual venues","description":"Craftsteak, Morimoto, Italian-American dining, bars and faster choices cover a wide range of budgets."},
        {"category":"entertainment","name":"KÀ, arena, comedy and nightlife","description":"The resort has one of the Strip's densest entertainment programmes, especially on major event weekends."}
      ],
      "faqs": [
        {"question":"Is MGM Grand casino open 24 hours?","answer":"Yes, the main casino operates 24 hours. Poker, sportsbook services, restaurants and shows use separate schedules."},
        {"question":"What games are available?","answer":"The official offer includes slots, video poker, electronic table games, live tables, poker and BetMGM sports betting."},
        {"question":"How old must you be to gamble?","answer":"You must be 21 or over to gamble at MGM Grand. Carry valid government-issued photo ID."},
        {"question":"Does MGM Grand have direct public transport?","answer":"Yes. The Las Vegas Monorail has an MGM Grand station, and Strip buses, taxis and ride services also serve the resort."}
      ],
      "editorial_source_urls": [
        "https://mgmgrand.mgmresorts.com/en/casino.html",
        "https://mgmgrand.mgmresorts.com/en/contact-us.html",
        "https://mgmgrand.mgmresorts.com/en.html"
      ]
    },
    {
      "id": 3636,
      "expected_name": "Borgata Hotel Casino and Spa",
      "short_description": "Atlantic City resort for casino, poker and dining",
      "summary": "Borgata is a major Atlantic City resort in the Marina District, combining a large casino and poker room with two hotel experiences, spa, restaurants, nightlife and live entertainment. The property is operated by MGM Resorts International.",
      "editorial_title": "Borgata Hotel Casino and Spa guide",
      "editorial_paragraphs": [
        "Borgata sits away from the Boardwalk in Atlantic City's Marina District and feels more like a self-contained resort than a walk-in seaside casino. The original Borgata hotel and MGM Tower connect directly to gaming, dining, spa and entertainment spaces, making the property practical for an overnight stay.",
        "The casino offer includes a large selection of slots, live table games, a dedicated poker room and sports betting. Borgata's official game pages highlight baccarat, blackjack, craps, pai gow poker and roulette among the tables. The main floor is available around the clock, but poker schedules, tournaments and sportsbook services change.",
        "Dining ranges from B-Prime Steakhouse and Angeline to casual venues, a food hall and in-room service. The Music Box and other event spaces add concerts and comedy, while nightlife and the spa broaden the itinerary. Restaurant reservations are recommended on show nights and busy weekends."
      ],
      "games_description": "Borgata offers slots, live tables including baccarat, blackjack, craps, pai gow poker and roulette, a dedicated poker room and sports betting. Current poker games, tournaments and promotions are published by the resort.",
      "fun_facts": "Borgata and MGM Tower create two connected hotel experiences within one Marina District resort, with gaming and dining accessible indoors from both.",
      "seo_title": "Borgata Casino, Hotel, Poker & Dining Guide",
      "seo_description": "Plan Borgata Atlantic City with verified casino, poker, hotel, restaurants, access, parking, map, official links and FAQs.",
      "official_website_url": "https://www.theborgata.com/",
      "street_address": "1 Borgata Way, Atlantic City, NJ 08401, United States",
      "phone": "+1 609-317-1000",
      "verified_opened_on": "2003-07-02",
      "verified_operator_name": "MGM Resorts International",
      "hotel_name": "Borgata Hotel and MGM Tower",
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": true,
      "has_bingo": false,
      "key_facts": [
        {"label":"Opened","value":"2003"},
        {"label":"Minimum age","value":"21+"},
        {"label":"Casino hours","value":"24 hours"},
        {"label":"District","value":"Atlantic City Marina"}
      ],
      "editorial_highlights": [
        {"title":"Strong poker identity","text":"A dedicated poker room and regular events make poker one of Borgata's defining gaming products."},
        {"title":"Two connected hotels","text":"Borgata Hotel and MGM Tower serve different stay preferences within the same resort."},
        {"title":"Destination dining","text":"Fine dining, casual restaurants, a food hall and in-room service support an overnight visit."}
      ],
      "visit_details": {
        "hours":"The main casino offers 24-hour gaming. Poker, sportsbook desks, restaurants, spa and entertainment venues use individual hours.",
        "access":"Casino gaming and alcohol service are limited to guests aged 21 or over. Bring valid government-issued photo identification.",
        "getting_there":"Borgata is in Atlantic City's Marina District, separate from the Boardwalk. It is reached most easily by car, taxi or ride service, with regional bus options also available.",
        "parking":"A large on-site garage and valet service are available. Current fees, MGM Rewards benefits and event conditions should be checked online."
      },
      "amenities": [
        {"category":"hotel","name":"Borgata Hotel and MGM Tower","description":"Two connected hotel products offer direct access to the casino, dining, entertainment and spa facilities."},
        {"category":"dining","name":"Fine dining to food hall","description":"B-Prime, Angeline, Old Homestead and casual venues create a broad restaurant selection."},
        {"category":"entertainment","name":"Music Box, nightlife and spa","description":"Live shows, bars, nightlife and wellness facilities make Borgata a complete Marina District resort."}
      ],
      "faqs": [
        {"question":"Is Borgata casino open 24 hours?","answer":"The main casino operates 24 hours. Poker, sportsbook, restaurants and entertainment venues publish separate schedules."},
        {"question":"Does Borgata have a poker room?","answer":"Yes. Borgata operates a dedicated poker room with cash games, tournaments and schedules published on the official site."},
        {"question":"How old must you be to gamble?","answer":"Guests must be 21 or older to gamble or drink alcohol. Valid photo ID may be required."},
        {"question":"Is Borgata on the Atlantic City Boardwalk?","answer":"No. Borgata is in the Marina District. Visitors should plan transport rather than assume it is a short Boardwalk walk."}
      ],
      "editorial_source_urls": [
        "https://www.theborgata.com/",
        "https://www.theborgata.com/casino",
        "https://www.theborgata.com/contact-us/"
      ]
    },
    {
      "id": 3929,
      "expected_name": "Spielbank Baden Baden",
      "short_description": "Historic Kurhaus casino with formal European gaming rooms",
      "summary": "Casino Baden-Baden occupies ornate rooms inside the Kurhaus and combines classic table gaming, poker and slot machines with a restaurant and guided visits. The historic setting, entry rules and dress expectations make planning more important than at a casual gaming hall.",
      "editorial_title": "Casino Baden-Baden: history and practical details",
      "editorial_paragraphs": [
        "Casino Baden-Baden is defined by its setting in the Kurhaus. Chandeliers, painted ceilings and formal salons give the classic gaming rooms a strong nineteenth-century atmosphere, while a separate slot-machine area provides a less ceremonial experience. Guided tours allow daytime visitors to see the interiors without taking part in gaming.",
        "The official gaming offer includes roulette, blackjack, Ultimate Texas Hold'em, poker events and certified slot machines. Classic tables open later than the slot area and hours extend on Friday and Saturday. The casino also charges admission for classic gaming, so visitors should check the current fee and table timetable.",
        "Entry standards are more explicit than in many modern resorts. The minimum age is 21, recognised identification is required and the classic rooms expect appropriate clothing. The Grill restaurant and Club Bernstein provide food and nightlife, while accommodation is spread across Baden-Baden rather than attached to the casino."
      ],
      "games_description": "The official programme covers roulette, blackjack, Ultimate Texas Hold'em, poker and slot machines. Classic gaming and machines have different opening hours, and tournament schedules are published separately.",
      "fun_facts": "The gaming rooms are inside Baden-Baden's Kurhaus, making architecture and guided tours an important part of the venue's appeal even for non-players.",
      "seo_title": "Casino Baden-Baden: Games, Hours & Entry Guide",
      "seo_description": "Visit Casino Baden-Baden with verified games, opening hours, age and dress rules, dining, map, official links and FAQs.",
      "official_website_url": "https://www.casino-baden-baden.de/en",
      "street_address": "Kaiserallee 1, 76530 Baden-Baden, Germany",
      "phone": "+49 7221 30240",
      "verified_opened_on": null,
      "verified_operator_name": "Baden-Württembergische Spielbanken",
      "hotel_name": null,
      "always_open": false,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": false,
      "has_bingo": false,
      "key_facts": [
        {"label":"Minimum age","value":"21+"},
        {"label":"Location","value":"Kurhaus Baden-Baden"},
        {"label":"Classic gaming entry","value":"Paid admission"},
        {"label":"Hotel on site","value":"No"}
      ],
      "editorial_highlights": [
        {"title":"Historic salons","text":"The Kurhaus rooms provide one of Europe's most ornate and recognisable casino interiors."},
        {"title":"Two operating rhythms","text":"Slot machines open earlier, while classic tables begin later and run longer on weekends."},
        {"title":"Formal entry conditions","text":"Minimum age, recognised ID, admission fee and clothing standards should all be checked before arrival."}
      ],
      "visit_details": {
        "hours":"Slots currently open from 11 a.m.; closing is 2 a.m. Sunday to Thursday and 3 a.m. Friday and Saturday. Classic table hours vary by day, generally beginning between 3 p.m. and 5 p.m. Check the official timetable for each game.",
        "access":"Entry is restricted to guests aged 21 or over with recognised identification. Classic gaming charges an admission fee and applies clothing standards; review the official entry requirements.",
        "getting_there":"The casino is inside the Kurhaus at Kaiserallee 1 in central Baden-Baden, within walking distance of the town centre and major hotels.",
        "parking":"Central and Kurhaus parking options are available nearby. Confirm opening hours and rates with the local facility used."
      },
      "amenities": [
        {"category":"dining","name":"The Grill","description":"The casino restaurant serves evening meals with hours aligned broadly to the gaming programme; kitchen times end earlier than the casino."},
        {"category":"entertainment","name":"Guided tours and events","description":"Daytime tours and event programming let visitors experience the historic rooms outside normal table hours."},
        {"category":"hotel","name":"Baden-Baden accommodation","description":"There is no attached casino hotel, but the central spa town has luxury and independent hotels within easy reach."}
      ],
      "faqs": [
        {"question":"What is the minimum age at Casino Baden-Baden?","answer":"Guests must be at least 21 to enter the casino. Recognised photo identification is required."},
        {"question":"Is there a dress code?","answer":"The classic gaming rooms apply clothing standards. Review the official entry-requirements page before arriving, especially for footwear and evening wear."},
        {"question":"Do slots and table games have the same hours?","answer":"No. Slot machines open earlier, while classic tables begin later and schedules vary by game and day."},
        {"question":"Can the casino be visited without gambling?","answer":"Yes. Guided tours provide access to the historic interiors at scheduled times. Advance booking and tour availability should be checked online."}
      ],
      "editorial_source_urls": [
        "https://www.casino-baden-baden.de/en",
        "https://www.casino-baden-baden.de/en/the-casino/opening-hours-and-admission-fees",
        "https://www.casino-baden-baden.de/en/the-casino/entry-requirements/"
      ]
    },
    {
      "id": 3931,
      "expected_name": "Grand Lisboa Casino & Hotel",
      "short_description": "Central Macao landmark with casino and fine dining",
      "summary": "Grand Lisboa is a distinctive central Macao casino hotel operated by SJM Resorts. Its landmark tower, multi-level gaming, hotel and acclaimed restaurants make it a city-centre counterpart to the newer integrated resorts in Cotai.",
      "editorial_title": "Grand Lisboa Casino and Hotel guide",
      "editorial_paragraphs": [
        "Grand Lisboa is one of the defining shapes on the Macao skyline. Unlike the broad resort campuses of Cotai, it rises from the established casino district close to the older Lisboa complex and central city streets, making it useful for visitors combining gaming with historic Macao.",
        "The casino provides live tables and electronic or slot gaming across multiple levels. Baccarat is central, with other table formats offered according to current floor configuration. Because Macao casinos regularly adjust layouts and minimums, the current public floor should take precedence over historical machine or table counts.",
        "The attached hotel is especially well known for dining. Robuchon au Dôme occupies the upper dome with panoramic views, while other restaurants include Chinese and international options plus round-the-clock casual service. Guests can therefore use Grand Lisboa as a hotel and dining destination as well as a casino."
      ],
      "games_description": "The casino offers live table gaming, slots and electronic formats, with baccarat prominent and other games available according to the current floor. Public and premium areas may apply different minimums and entry conditions.",
      "fun_facts": "Robuchon au Dôme sits high in the landmark tower, pairing fine dining with one of the most elevated restaurant views in central Macao.",
      "seo_title": "Grand Lisboa Macau Casino, Hotel & Dining Guide",
      "seo_description": "Explore Grand Lisboa Macau with sourced casino, hotel, dining, access, transport, map, official links and FAQs.",
      "official_website_url": "https://www.grandlisboahotels.com/en",
      "street_address": "Avenida de Lisboa, Macao",
      "phone": "+853 2828 3838",
      "verified_opened_on": "2007-02-11",
      "verified_operator_name": "SJM Resorts, S.A.",
      "hotel_name": "Grand Lisboa Hotel",
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": false,
      "has_bingo": false,
      "key_facts": [
        {"label":"Casino opened","value":"2007"},
        {"label":"Minimum age","value":"21+"},
        {"label":"Area","value":"Central Macao"},
        {"label":"Hotel","value":"Grand Lisboa"}
      ],
      "editorial_highlights": [
        {"title":"Central city location","text":"Grand Lisboa sits in Macao's established casino district rather than on the Cotai Strip."},
        {"title":"Multi-level gaming","text":"Live tables and electronic gaming are distributed through the distinctive tower complex."},
        {"title":"Destination restaurant","text":"Robuchon au Dôme and the wider hotel dining programme are major reasons to visit beyond gaming."}
      ],
      "visit_details": {
        "hours":"The casino operates daily with extended and generally round-the-clock gaming access. Restaurants and individual casino areas publish separate schedules.",
        "access":"Casino entry in Macao is restricted to guests aged 21 or over. International visitors should carry a passport; premium areas may apply additional requirements.",
        "getting_there":"Grand Lisboa is on Avenida de Lisboa in central Macao, convenient for taxis, public buses and walking connections to the historic centre and nearby casino district.",
        "parking":"On-site and nearby parking options serve the hotel district. Confirm guest validation and public access directly with the hotel."
      },
      "amenities": [
        {"category":"hotel","name":"Grand Lisboa Hotel","description":"The attached hotel provides rooms and suites with direct access to the casino and restaurant collection."},
        {"category":"dining","name":"Robuchon au Dôme","description":"The tower's signature fine-dining restaurant offers panoramic views, a formal dress code and advance reservations."},
        {"category":"dining","name":"Chinese and 24-hour options","description":"Additional hotel restaurants and round-the-clock casual dining broaden the food programme."}
      ],
      "faqs": [
        {"question":"Is Grand Lisboa in Cotai?","answer":"No. Grand Lisboa is in central Macao, in the established casino district near the historic city centre."},
        {"question":"What is the minimum casino age?","answer":"Guests must be at least 21 to enter casino gaming areas in Macao. Carry valid identification."},
        {"question":"Does Grand Lisboa have a hotel?","answer":"Yes. Grand Lisboa Hotel is integrated with the casino and restaurant complex."},
        {"question":"What games are available?","answer":"The casino offers live tables, slots and electronic games, with baccarat prominent. Exact games and minimums vary by floor and time."}
      ],
      "editorial_source_urls": [
        "https://www.grandlisboahotels.com/en",
        "https://www.grandlisboahotels.com/en/dining/robuchon-au-dome"
      ]
    },
    {
      "id": 4004,
      "expected_name": "Resorts World Casino Sentosa",
      "short_description": "Sentosa integrated resort with gaming, hotels and attractions",
      "summary": "Resorts World Sentosa combines a Singapore casino with several hotels, restaurants and major leisure attractions on Sentosa Island. The integrated format makes it particularly suitable for mixed groups and multi-day stays.",
      "editorial_title": "Resorts World Sentosa casino and resort guide",
      "editorial_paragraphs": [
        "Resorts World Sentosa was Singapore's first integrated resort and remains closely tied to the island's leisure programme. The casino sits within a destination that also includes multiple hotels, dining, shopping and large attractions, so visitors should plan by zone rather than assume every venue shares one entrance or schedule.",
        "The casino offers live table games, slot machines and electronic formats, with public and premium gaming areas. Singapore applies specific entry controls: all guests must be at least 21, and Singapore citizens and permanent residents are subject to the statutory casino entry levy unless exempt.",
        "The resort's strongest non-gaming advantage is variety. Hotels serve different budgets and styles, while the dining directory covers fine restaurants, Asian food, casual venues, bars and cafés. Theme-park and marine attractions make the destination practical for families and groups, although under-21 guests cannot enter the casino."
      ],
      "games_description": "The casino offers live table gaming, slot machines and electronic games across public and premium areas. Game availability, minimums and premium access conditions should be confirmed with the resort.",
      "fun_facts": "Resorts World Sentosa integrates casino gaming into a broader island destination with hotels, dining, theme-park entertainment and marine attractions.",
      "seo_title": "Resorts World Sentosa Casino & Visit Guide",
      "seo_description": "Plan Resorts World Sentosa with verified casino entry, hotels, dining, transport, map, official sources and practical FAQs.",
      "official_website_url": "https://www.rwsentosa.com/en",
      "street_address": "8 Sentosa Gateway, Singapore 098269",
      "phone": "+65 6577 8888",
      "verified_opened_on": "2010-02-14",
      "verified_operator_name": "Resorts World at Sentosa Pte. Ltd.",
      "hotel_name": "Resorts World Sentosa hotels",
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": false,
      "has_bingo": false,
      "key_facts": [
        {"label":"Opened","value":"2010"},
        {"label":"Minimum age","value":"21+"},
        {"label":"Location","value":"Sentosa Island"},
        {"label":"Local entry rule","value":"Levy may apply"}
      ],
      "editorial_highlights": [
        {"title":"Integrated island resort","text":"Casino, hotels, restaurants and major leisure attractions share one Sentosa destination."},
        {"title":"Controlled casino entry","text":"A 21+ age rule applies, with a statutory levy for Singapore citizens and permanent residents unless exempt."},
        {"title":"Built for mixed groups","text":"Non-gaming attractions and a broad dining programme give under-21 and non-gaming visitors substantial alternatives."}
      ],
      "visit_details": {
        "hours":"The integrated resort operates daily and the casino provides extended access, but individual tables, restaurants and attractions use separate schedules. Confirm same-day information online.",
        "access":"Casino guests must be at least 21. Singapore citizens and permanent residents must meet the statutory entry-levy rules; foreign visitors should carry a passport for identification.",
        "getting_there":"The resort is at 8 Sentosa Gateway. It can be reached by Sentosa Express, public bus, taxi, ride service, cable car connections and pedestrian access from VivoCity.",
        "parking":"Large on-site parking serves the integrated resort. Rates and peak-day conditions vary, especially during holidays and major events."
      },
      "amenities": [
        {"category":"hotel","name":"Multiple resort hotels","description":"Several hotels provide different styles and price points within walking distance of gaming, dining and attractions."},
        {"category":"dining","name":"Wide dining directory","description":"Fine dining, Chinese and Japanese restaurants, casual halls, bars and cafés serve both casino guests and general resort visitors."},
        {"category":"entertainment","name":"Sentosa attractions","description":"Theme-park, marine and event experiences make the resort a broader leisure destination."}
      ],
      "faqs": [
        {"question":"What is the minimum age for the casino?","answer":"Guests must be at least 21 to enter casino areas. Valid identification is required."},
        {"question":"Do Singapore residents pay an entry levy?","answer":"Singapore citizens and permanent residents are subject to the statutory casino entry levy unless an exemption applies. Check current government and resort guidance."},
        {"question":"Are there hotels at Resorts World Sentosa?","answer":"Yes. The integrated resort includes multiple hotels with direct or short walking access to dining, attractions and the casino."},
        {"question":"Can families visit the resort?","answer":"Yes. Many hotels, restaurants and attractions are family-oriented, but anyone under 21 is excluded from the casino."}
      ],
      "editorial_source_urls": [
        "https://www.rwsentosa.com/en",
        "https://www.rwsentosa.com/en/about",
        "https://www.rwsentosa.com/en/dine"
      ]
    },
    {
      "id": 4023,
      "expected_name": "Marina Bay Sands Casino",
      "short_description": "Singapore landmark resort with 24-hour casino dining",
      "summary": "Marina Bay Sands is a landmark Singapore integrated resort combining a large casino with a five-star hotel, SkyPark, shopping, theatres, museum, extensive dining and convention facilities. The casino sits inside a destination designed for full-day and overnight visits.",
      "editorial_title": "Marina Bay Sands Casino visitor guide",
      "editorial_paragraphs": [
        "Marina Bay Sands is immediately recognisable by its three hotel towers and rooftop SkyPark. At ground and podium levels, the complex links the casino with The Shoppes, theatres, ArtScience Museum, restaurants and convention spaces, so the property functions as a city landmark even for visitors who never enter gaming areas.",
        "The casino provides mass gaming floors and access-controlled higher-limit areas with live tables, slot machines and electronic gaming. Official information also highlights 24-hour food options inside the casino. Singapore entry rules are strict: guests must be 21 or over, while citizens and permanent residents are subject to the entry levy unless exempt.",
        "Dining is a major part of the resort, with the official directory listing more than 45 restaurants across local, regional and international cuisines. The attached hotel, rooftop attractions, shopping, museum and shows make Marina Bay Sands one of the easiest casinos in Asia to combine with a broader city itinerary."
      ],
      "games_description": "The casino includes live table games, slot machines and electronic gaming on public floors, plus access-controlled higher-limit areas. Specific games, table minimums and premium access conditions change and should be confirmed on site.",
      "fun_facts": "The rooftop SkyPark spans the three hotel towers, while the casino, shopping, museum, theatres and convention centre are connected through the podium below.",
      "seo_title": "Marina Bay Sands Casino & Singapore Visit Guide",
      "seo_description": "Plan Marina Bay Sands Casino with verified entry rules, games, hotel, dining, transport, map, official sources and FAQs.",
      "official_website_url": "https://www.marinabaysands.com/casino.html",
      "street_address": "10 Bayfront Avenue, Singapore 018956",
      "phone": "+65 6688 8888",
      "verified_opened_on": "2010-04-27",
      "verified_operator_name": "Marina Bay Sands Pte. Ltd.",
      "hotel_name": "Marina Bay Sands",
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": false,
      "has_bingo": false,
      "key_facts": [
        {"label":"Opened","value":"2010"},
        {"label":"Minimum age","value":"21+"},
        {"label":"Casino access","value":"24 hours"},
        {"label":"Local entry rule","value":"Levy may apply"}
      ],
      "editorial_highlights": [
        {"title":"City landmark","text":"The three towers and SkyPark make the resort a major Singapore destination independently of the casino."},
        {"title":"24-hour casino services","text":"The gaming floors and selected food options support overnight and early-morning visits."},
        {"title":"Extensive dining","text":"More than 45 restaurants cover local and international cuisines across the connected complex."}
      ],
      "visit_details": {
        "hours":"The casino is open 24 hours. Individual restaurants, attractions, shops and entertainment venues have separate operating schedules.",
        "access":"Entry is restricted to guests aged 21 or over. Singapore citizens and permanent residents must comply with the statutory entry levy; foreign visitors should carry a passport.",
        "getting_there":"Bayfront MRT station connects directly to the resort. Taxis, ride services and buses also serve entrances along Bayfront Avenue.",
        "parking":"Large on-site parking is available beneath the complex. Rates and validation programmes vary by venue and event."
      },
      "amenities": [
        {"category":"hotel","name":"Marina Bay Sands hotel","description":"The attached tower hotel provides direct access to the casino, restaurants, shopping and SkyPark facilities."},
        {"category":"dining","name":"More than 45 restaurants","description":"The official directory spans Singaporean, Asian and international food, celebrity chefs and casino-floor dining."},
        {"category":"entertainment","name":"SkyPark, museum, theatres and shops","description":"Major non-gaming attractions make the resort suitable for mixed groups and city sightseeing."}
      ],
      "faqs": [
        {"question":"Is Marina Bay Sands Casino open 24 hours?","answer":"Yes. The casino operates 24 hours, while restaurants, attractions and shops use individual schedules."},
        {"question":"What is the minimum age to enter?","answer":"Guests must be at least 21. Valid identification is required and foreign visitors should carry a passport."},
        {"question":"Do Singapore residents pay a levy?","answer":"Singapore citizens and permanent residents are subject to the statutory casino entry levy unless exempt. Check the current rules before visiting."},
        {"question":"Is the casino connected to the hotel and MRT?","answer":"Yes. The casino is part of the integrated resort, and Bayfront MRT station connects directly to the complex."}
      ],
      "editorial_source_urls": [
        "https://www.marinabaysands.com/casino.html",
        "https://www.marinabaysands.com/",
        "https://www.marinabaysands.com/restaurants/view-all.html"
      ]
    },
    {
      "id": 4115,
      "expected_name": "Sun City Casino Resort",
      "short_description": "South African resort with casino, hotels and outdoor leisure",
      "summary": "Sun City is a large North West province resort combining casino gaming with four accommodation areas, restaurants, golf, the Valley of Waves water park and family activities. The destination is designed for multi-day stays rather than a short urban casino visit.",
      "editorial_title": "Sun City Casino Resort planning guide",
      "editorial_paragraphs": [
        "Sun City is a destination resort set away from major city centres, so transport and accommodation are central to planning. The Palace, Cascades, Soho Hotel and Cabanas serve different budgets and styles, while shuttles and pedestrian routes connect the resort's main leisure areas.",
        "The casino offers slot machines and live table games within Sun Central and the broader resort. Current games, tables and promotions should be confirmed on the official casino page, as the operational mix is more useful than historical machine counts.",
        "Sun City's main distinction is the amount to do beyond gaming. Two Gary Player-designed golf courses, Valley of Waves, family activities, restaurants and bars make it possible to build a complete holiday. Day visitors should check capacity, ticketing and access conditions before making the drive."
      ],
      "games_description": "Sun City Casino offers slot machines and live table games, with current promotions and operating information published by Sun International. Exact tables and minimums vary.",
      "fun_facts": "The resort combines casino gaming with the Palace, the Valley of Waves and two golf courses, creating one of Africa's best-known integrated leisure destinations.",
      "seo_title": "Sun City Casino Resort: Games, Hotels & Guide",
      "seo_description": "Plan Sun City Casino Resort with verified games, hotels, dining, access, parking, map, official sources and practical FAQs.",
      "official_website_url": "https://www.suninternational.com/sun-city/casino",
      "street_address": "Sun City Resort, R556, Sun City, North West 0316, South Africa",
      "phone": "+27 14-557-1000",
      "verified_opened_on": "1979-12-07",
      "verified_operator_name": "Sun International",
      "hotel_name": "The Palace, Cascades, Soho Hotel and Cabanas",
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": false,
      "has_bingo": false,
      "key_facts": [
        {"label":"Opened","value":"1979"},
        {"label":"Minimum age","value":"18+"},
        {"label":"Resort type","value":"Destination resort"},
        {"label":"Accommodation","value":"Four main options"}
      ],
      "editorial_highlights": [
        {"title":"Multi-day destination","text":"Distance from major cities and the breadth of activities make an overnight stay more practical than a quick visit."},
        {"title":"Casino at Sun Central","text":"Slots and live tables sit within the resort's central entertainment and dining area."},
        {"title":"Outdoor attractions","text":"Golf, Valley of Waves and family activities distinguish Sun City from an urban casino hotel."}
      ],
      "visit_details": {
        "hours":"The casino provides extended daily access, while tables, restaurants and resort attractions use separate schedules. Confirm current casino and day-visitor hours before travelling.",
        "access":"Casino gaming is restricted to guests aged 18 or over with accepted photo identification. Resort day-visitor tickets and attraction access use separate rules.",
        "getting_there":"Sun City is in North West province and is normally reached by car, private transfer or organised shuttle from Johannesburg, Pretoria or nearby airports.",
        "parking":"On-site parking serves hotel guests and day visitors. Day capacity can be limited, so review current ticketing and entrance instructions."
      },
      "amenities": [
        {"category":"hotel","name":"Four accommodation areas","description":"The Palace, Cascades, Soho Hotel and Cabanas cover luxury, resort and family-oriented stays."},
        {"category":"dining","name":"Restaurants and bars across the resort","description":"Fine dining, family restaurants, fast food and themed bars are distributed between the hotels and Sun Central."},
        {"category":"entertainment","name":"Valley of Waves and golf","description":"A water park, two golf courses and daily activities create a substantial non-gaming itinerary."}
      ],
      "faqs": [
        {"question":"How old must you be to enter Sun City Casino?","answer":"Casino gaming is restricted to guests aged 18 or over. Bring accepted photo identification."},
        {"question":"Can Sun City be visited for the day?","answer":"Yes, but day-visitor capacity, tickets and attraction access can be limited. Check the official rates and access page before travelling."},
        {"question":"Does Sun City have hotels?","answer":"Yes. The resort includes The Palace, Cascades, Soho Hotel and Cabanas, plus vacation-club accommodation."},
        {"question":"What games are available?","answer":"The casino offers slot machines and live table gaming. Current games, promotions and hours are published by Sun International."}
      ],
      "editorial_source_urls": [
        "https://www.suninternational.com/sun-city",
        "https://www.suninternational.com/sun-city/casino",
        "https://www.suninternational.com/sun-city/restaurants"
      ]
    },
    {
      "id": 4270,
      "expected_name": "Mohegan Sun",
      "short_description": "Connecticut resort with two casinos, arena and hotels",
      "summary": "Mohegan Sun is a large Connecticut resort operated by Mohegan, with Casino of the Earth and Casino of the Sky, two hotel towers, an arena, extensive dining, shopping, nightlife and a sportsbook. The property is large enough to require route planning.",
      "editorial_title": "Mohegan Sun casino and resort guide",
      "editorial_paragraphs": [
        "Mohegan Sun is organised around two principal casinos, Earth and Sky, linked to hotels, shops, restaurants, an expo centre and entertainment venues. The scale means visitors should identify the nearest entrance or garage for their main destination, especially on arena and convention dates.",
        "The current official gaming guide describes nearly 4,000 slots, more than 300 table games and a 33-table poker room, alongside the FanDuel Sportsbook. Those figures are useful as a snapshot, but individual tables, poker formats and cashier schedules should still be checked for the planned visit.",
        "The resort's non-gaming offer includes two hotel towers, more than 40 dining choices, a 10,000-seat arena, shops, spas and nightlife. That breadth makes Mohegan Sun suitable for an overnight entertainment trip rather than only a casino stop."
      ],
      "games_description": "Mohegan Sun's official 2026 guide lists nearly 4,000 slots, more than 300 table games, a 33-table poker room and FanDuel sports betting. Live availability and service hours vary by area.",
      "fun_facts": "Casino of the Earth and Casino of the Sky give the resort two distinct gaming environments connected to a much larger hotel, dining, retail and entertainment complex.",
      "seo_title": "Mohegan Sun Casino, Hotel, Games & Visit Guide",
      "seo_description": "Plan Mohegan Sun with verified games, hotels, restaurants, arena, access, parking, map, official links and FAQs.",
      "official_website_url": "https://mohegansun.com/",
      "street_address": "1 Mohegan Sun Boulevard, Uncasville, CT 06382, United States",
      "phone": "+1 888-226-7711",
      "verified_opened_on": "1996-10-12",
      "verified_operator_name": "Mohegan",
      "hotel_name": "Sky Tower and Earth Tower",
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": true,
      "has_bingo": false,
      "key_facts": [
        {"label":"Opened","value":"1996"},
        {"label":"Minimum age","value":"21+"},
        {"label":"Main casinos","value":"Earth and Sky"},
        {"label":"Arena capacity","value":"10,000"}
      ],
      "editorial_highlights": [
        {"title":"Two large casinos","text":"Casino of the Earth and Casino of the Sky create distinct gaming zones inside one connected resort."},
        {"title":"Current scale","text":"The official guide lists nearly 4,000 slots, 300+ tables and a 33-table poker room."},
        {"title":"Major entertainment resort","text":"Two hotels, an arena, dining, shops, nightlife and spas support a complete overnight programme."}
      ],
      "visit_details": {
        "hours":"The main casino areas operate 24 hours. Poker, sportsbook cashier windows, restaurants, shops and entertainment venues publish individual schedules.",
        "access":"Casino gaming is generally restricted to guests aged 21 or over. Many resort restaurants, shops and entertainment areas welcome younger visitors via designated routes.",
        "getting_there":"Mohegan Sun is in Uncasville, Connecticut and is primarily reached by car, coach or regional transport. The official site lists options by car, bus, train, ferry and plane.",
        "parking":"Multiple garages and valet entrances serve different resort zones. Choose parking based on the casino, hotel, arena or restaurant you plan to visit."
      },
      "amenities": [
        {"category":"hotel","name":"Sky Tower and Earth Tower","description":"Two hotel towers provide direct resort access and different room, spa and meeting options."},
        {"category":"dining","name":"More than 40 dining choices","description":"The official directory covers fine dining, casual restaurants, cafés, bars and family-friendly menus."},
        {"category":"entertainment","name":"Arena, Wolf Den and nightlife","description":"A 10,000-seat arena, live venues, comedy, shopping and nightlife create a dense event calendar."}
      ],
      "faqs": [
        {"question":"Is Mohegan Sun open 24 hours?","answer":"The main casino areas operate 24 hours. Poker, sportsbook services, dining and entertainment venues have separate schedules."},
        {"question":"How old must you be to gamble?","answer":"Casino gaming is generally limited to guests aged 21 or over. Valid photo ID may be required."},
        {"question":"How large is the gaming offer?","answer":"The current official guide lists nearly 4,000 slots, more than 300 table games and a 33-table poker room."},
        {"question":"Does Mohegan Sun have hotels?","answer":"Yes. Sky Tower and Earth Tower are integrated with the casino, dining, retail and entertainment complex."}
      ],
      "editorial_source_urls": [
        "https://mohegansun.com/",
        "https://mohegansun.com/playing.html",
        "https://mohegansun.com/about-mohegan-sun.html"
      ]
    },
    {
      "id": 4332,
      "expected_name": "Casino de Monte-Carlo",
      "short_description": "Belle Époque gaming landmark on Place du Casino",
      "summary": "Casino de Monte-Carlo is Monaco's historic gaming landmark, occupying Charles Garnier-designed Belle Époque rooms on Place du Casino. Daytime visits, afternoon gaming, formal salons and on-site restaurants make it both an architectural attraction and an active casino.",
      "editorial_title": "Casino de Monte-Carlo: visit, games and etiquette",
      "editorial_paragraphs": [
        "Casino de Monte-Carlo is as much a monument as a gaming venue. The façade, atrium and ornate salons form the centrepiece of Place du Casino, opposite Hôtel de Paris and Café de Paris. Morning visits allow guests to see the architecture before the main gaming rooms begin their afternoon schedule.",
        "The casino offers European table games, slot machines and private or higher-limit salons according to the current programme. Roulette is particularly associated with the venue, alongside blackjack, baccarat and poker-derived tables. Room access, minimums and opening times vary, so the official daily information is the correct reference.",
        "Entry requires more preparation than a casual resort casino. Guests must be at least 18, present original identification and follow clothing standards for the gaming rooms. Le Salon Rose and Le Train Bleu provide on-site dining, while Hôtel de Paris offers luxury accommodation directly across the square rather than inside the casino building."
      ],
      "games_description": "The gaming rooms offer European roulette, blackjack, baccarat, poker-style table games and slot machines, with private salons and table availability determined by the current schedule.",
      "fun_facts": "The casino was designed in part by Charles Garnier, architect of the Paris Opera, and its ornate salons remain a central architectural attraction in Monaco.",
      "seo_title": "Casino de Monte-Carlo: Hours, Dress Code & Guide",
      "seo_description": "Plan Casino de Monte-Carlo with verified hours, age and dress rules, games, dining, map, official links and FAQs.",
      "official_website_url": "https://www.montecarlosbm.com/en/casino-monaco/casino-monte-carlo",
      "street_address": "Place du Casino, 98000 Monaco",
      "phone": "+377 98 06 21 21",
      "verified_opened_on": "1863-07-18",
      "verified_operator_name": "Monte-Carlo Société des Bains de Mer",
      "hotel_name": null,
      "always_open": false,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": false,
      "has_bingo": false,
      "key_facts": [
        {"label":"Opened","value":"1863"},
        {"label":"Minimum age","value":"18+"},
        {"label":"Public visits","value":"Morning"},
        {"label":"Gaming rooms","value":"From 2 p.m."}
      ],
      "editorial_highlights": [
        {"title":"Architectural landmark","text":"Belle Époque salons and Charles Garnier's design make the building a destination beyond gaming."},
        {"title":"Visit before play","text":"Public visits run in the morning, while the gaming rooms open from 2 p.m."},
        {"title":"Formal casino etiquette","text":"Original ID, age checks and clothing standards are part of the experience and must be planned in advance."}
      ],
      "visit_details": {
        "hours":"The official schedule currently lists public visits from 10 a.m. to 1 p.m. and gaming rooms from 2 p.m. Closing times and individual salons vary; check the venue page on the day.",
        "access":"Guests must be at least 18 and present original valid identification. Dress standards apply in the gaming rooms, with stricter expectations in some salons.",
        "getting_there":"The casino is on Place du Casino in central Monte-Carlo, accessible on foot from nearby hotels and by bus, taxi or train connections within Monaco.",
        "parking":"Public paid parking is available beneath and around Place du Casino. Valet services may be available through nearby SBM properties."
      },
      "amenities": [
        {"category":"dining","name":"Le Salon Rose and Le Train Bleu","description":"On-site restaurants provide Mediterranean and Italian dining inside the casino setting; hours and access should be reserved separately."},
        {"category":"entertainment","name":"Morning architectural visits","description":"Scheduled public visits make the ornate rooms accessible before casino gaming begins."},
        {"category":"hotel","name":"Hôtel de Paris opposite","description":"The casino building has no attached hotel, but Hôtel de Paris Monte-Carlo stands directly across Place du Casino."}
      ],
      "faqs": [
        {"question":"What is the minimum age at Casino de Monte-Carlo?","answer":"Guests must be at least 18 to enter the gaming rooms and must present original valid identification."},
        {"question":"Is there a dress code?","answer":"Yes. Clothing standards apply in the gaming rooms and some salons are more formal. Check the official entry guidance before arrival."},
        {"question":"Can you visit without gambling?","answer":"Yes. Public architectural visits are currently offered in the morning before the gaming rooms open."},
        {"question":"What time does gaming begin?","answer":"The official venue page currently lists the gaming rooms from 2 p.m. Individual rooms and closing times can vary."}
      ],
      "editorial_source_urls": [
        "https://www.montecarlosbm.com/en/casino-monaco/casino-monte-carlo",
        "https://www.montecarlosbm.com/en/casino-monaco/casino-monte-carlo/restaurants-monaco",
        "https://www.montecarlosbm.com/en/casino-monaco/casino-monte-carlo/room"
      ]
    },
    {
      "id": 5535,
      "expected_name": "The Hippodrome Casino London",
      "short_description": "24-hour West End casino inside a former theatre",
      "summary": "The Hippodrome Casino occupies a restored former theatre beside Leicester Square. Open 24 hours apart from Christmas Day, it combines several gaming floors, poker, bars, restaurants and live entertainment in a compact West End location.",
      "editorial_title": "The Hippodrome Casino London guide",
      "editorial_paragraphs": [
        "The Hippodrome's circular auditorium and balconies reveal the building's theatrical past immediately. Gaming, restaurants, bars and entertainment are arranged vertically across several floors, creating a compact but busy venue in the heart of the West End rather than a hotel-resort layout.",
        "The official gaming information describes multiple casino floors with roulette, blackjack, baccarat, slots, electronic games and a dedicated poker deck. The building is open 24 hours, but poker events, entertainment and individual food venues use their own timetables.",
        "The venue is easy to combine with a Leicester Square evening. Heliot Steak House overlooks the main gaming floor, while Chop Chop, bars, rooftop spaces and live shows add non-gaming options. There is no membership requirement and no formal universal dress code, although smart casual is the normal choice."
      ],
      "games_description": "The Hippodrome offers roulette, blackjack, baccarat, slot machines, electronic gaming and a dedicated poker deck across several floors. Tournament and cash-game schedules should be checked separately.",
      "fun_facts": "The casino preserves the dramatic volume of the former Hippodrome theatre, with Heliot Steak House looking down over the main gaming floor.",
      "seo_title": "Hippodrome Casino London: Hours, Games & Guide",
      "seo_description": "Plan The Hippodrome Casino London with verified 24-hour access, games, dining, transport, map, official links and FAQs.",
      "official_website_url": "https://www.hippodromecasino.com/",
      "street_address": "Cranbourn Street, Leicester Square, London WC2H 7JH, United Kingdom",
      "phone": "+44 20 7769 8888",
      "verified_opened_on": "2012-07-13",
      "verified_operator_name": "Hippodrome Casino Limited",
      "hotel_name": null,
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": false,
      "has_bingo": false,
      "key_facts": [
        {"label":"Casino opened","value":"2012"},
        {"label":"Minimum age","value":"18+"},
        {"label":"Opening","value":"24/7 except Christmas"},
        {"label":"Membership","value":"Not required"}
      ],
      "editorial_highlights": [
        {"title":"Former theatre setting","text":"The restored auditorium gives the main gaming floor a vertical, theatrical atmosphere."},
        {"title":"Open around the clock","text":"The building is open 24/7 except Christmas Day, though individual venues keep separate hours."},
        {"title":"Central West End position","text":"Leicester Square, Covent Garden and several Underground stations are within a short walk."}
      ],
      "visit_details": {
        "hours":"The Hippodrome is open 24 hours a day, seven days a week except Christmas Day. Restaurants, poker and entertainment have separate schedules.",
        "access":"Entry is for guests aged 18 or over. No membership is required. Bring valid photo ID; the venue says there is no fixed dress code, with smart casual the usual choice.",
        "getting_there":"The casino is beside Leicester Square. Leicester Square Underground is closest, with Covent Garden, Piccadilly Circus and Charing Cross also within walking distance.",
        "parking":"Central London parking is limited and expensive. Public transport is the practical default; nearby commercial car parks should be booked independently."
      },
      "amenities": [
        {"category":"dining","name":"Heliot Steak House","description":"A full-service steakhouse overlooks the main casino floor and operates on a separate evening schedule."},
        {"category":"dining","name":"Late-night food and bars","description":"Chop Chop, bars and lounges provide food and drink well into the night across the building."},
        {"category":"entertainment","name":"Live shows and rooftop spaces","description":"Cabaret, theatre, live events and terraces extend the venue beyond gaming."}
      ],
      "faqs": [
        {"question":"Is The Hippodrome Casino open 24 hours?","answer":"Yes. The building is open 24/7 except Christmas Day. Individual restaurants, poker and shows have separate hours."},
        {"question":"Do I need membership?","answer":"No membership is required. Guests must be 18 or over and should carry valid photo identification."},
        {"question":"Is there a dress code?","answer":"The venue states that it has no fixed dress code; smart casual is the most common choice."},
        {"question":"What is the nearest Underground station?","answer":"Leicester Square is the closest station. Covent Garden, Piccadilly Circus and Charing Cross are also nearby."}
      ],
      "editorial_source_urls": [
        "https://www.hippodromecasino.com/",
        "https://www.hippodromecasino.com/general-information/",
        "https://www.hippodromecasino.com/directions-maps/"
      ]
    },
    {
      "id": 5696,
      "expected_name": "Casino Barcelona",
      "short_description": "24-hour Port Olímpic casino with poker and dining",
      "summary": "Casino Barcelona is a waterfront entertainment venue at Port Olímpic with 24-hour slot areas, traditional tables, poker, restaurants, bars and live programming. Its city location makes it easy to combine with Barcelona's beaches and nightlife.",
      "editorial_title": "Casino Barcelona visitor guide",
      "editorial_paragraphs": [
        "Casino Barcelona sits beside Port Olímpic, close to the seafront and city beaches. It is an urban entertainment venue rather than an attached resort: gaming, food, bars and events are concentrated in one building, while accommodation is available throughout the surrounding waterfront and central city.",
        "The official schedule separates the 24-hour slot areas from traditional table games and poker cash hours. Visitors can expect slot machines, roulette, blackjack, baccarat, poker and sports-related gaming according to the current floor programme. Tournament calendars and live-table schedules should be checked before arrival.",
        "Dining includes Ají for Nikkei cuisine, Bet Bar, La Vinoteca and cocktail service, with several venues operating late. That mix suits an evening visit, but each restaurant has its own schedule and reservation policy even though parts of the casino remain open around the clock."
      ],
      "games_description": "Casino Barcelona offers 24-hour slots, traditional table games, poker cash games and sports-related gaming. The official visit page publishes separate schedules for the main hall, American room and poker.",
      "fun_facts": "The casino is directly beside Port Olímpic, making it one of the few major European casinos that can be combined easily with a waterfront walk and beach district visit.",
      "seo_title": "Casino Barcelona: Hours, Poker, Dining & Guide",
      "seo_description": "Plan Casino Barcelona with verified 24-hour slots, table and poker hours, restaurants, access, transport, map and FAQs.",
      "official_website_url": "https://www.casinobarcelona.com/en",
      "street_address": "Marina 19-21, 08005 Barcelona, Spain",
      "phone": "+34 900 354 354",
      "verified_opened_on": null,
      "verified_operator_name": "Grup Peralada",
      "hotel_name": null,
      "always_open": true,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": true,
      "has_bingo": false,
      "key_facts": [
        {"label":"Minimum age","value":"18+"},
        {"label":"Slot areas","value":"24 hours"},
        {"label":"Location","value":"Port Olímpic"},
        {"label":"Hotel on site","value":"No"}
      ],
      "editorial_highlights": [
        {"title":"Waterfront city location","text":"Port Olímpic, beaches and central Barcelona transport are close to the casino."},
        {"title":"Separate gaming schedules","text":"Slots run 24 hours while traditional tables and poker publish defined operating periods."},
        {"title":"Late-night dining","text":"Ají, Bet Bar, La Vinoteca and bars support a complete evening in the same building."}
      ],
      "visit_details": {
        "hours":"The official schedule lists slots and the American Room 24 hours. Poker cash currently runs from 2 p.m. to 6 a.m. and traditional tables from 4 p.m. to 5 a.m.; confirm same-day hours online.",
        "access":"Casino entry is restricted to guests aged 18 or over. Bring valid original photo identification and review current admission conditions.",
        "getting_there":"The casino is at Marina 19-21 in Port Olímpic. Ciutadella-Vila Olímpica metro, T5/T6 tram, buses, taxis and seafront walking routes serve the area.",
        "parking":"Paid parking is available in the Port Olímpic area. Check current casino validation and local garage rates before driving."
      },
      "amenities": [
        {"category":"dining","name":"Ají, Bet Bar and La Vinoteca","description":"Nikkei dining, quick food and late-night restaurant-bar service cover several styles within the venue."},
        {"category":"entertainment","name":"Live music and events","description":"Programming extends beyond gaming and should be checked against the current venue calendar."},
        {"category":"hotel","name":"Stay around Port Olímpic","description":"There is no attached casino hotel, but waterfront and central Barcelona hotels are widely available nearby."}
      ],
      "faqs": [
        {"question":"Is Casino Barcelona open 24 hours?","answer":"The slot areas and American Room are listed as open 24 hours. Traditional tables, poker and restaurants use separate schedules."},
        {"question":"How old must you be to enter?","answer":"Guests must be at least 18 and should carry valid original photo identification."},
        {"question":"Does Casino Barcelona have poker?","answer":"Yes. Poker cash games and events are offered on a separate schedule published by the casino."},
        {"question":"Is there a hotel attached?","answer":"No. Casino Barcelona is an urban entertainment venue, with hotels available around Port Olímpic and across the city."}
      ],
      "editorial_source_urls": [
        "https://www.casinobarcelona.com/en",
        "https://www.casinobarcelona.com/en/visit-us",
        "https://www.casinobarcelona.com/en/gastronomy/"
      ]
    },
    {
      "id": 6159,
      "expected_name": "Crown Casino and Entertainment Complex",
      "short_description": "Southbank casino resort with hotels and extensive dining",
      "summary": "Crown Melbourne is a large Southbank entertainment complex combining casino gaming with Crown Towers, Crown Metropol and Crown Promenade, a broad restaurant collection, bars, shopping, spa and event spaces beside the Yarra River.",
      "editorial_title": "Crown Melbourne casino and resort guide",
      "editorial_paragraphs": [
        "Crown Melbourne occupies a long stretch of the Southbank riverfront. The casino, three hotels, restaurants, bars, retail and event facilities are connected across a large complex, so visitors should choose a meeting point and entrance before arrival rather than relying on a single lobby.",
        "Gaming includes slot machines, electronic formats and live table games, with separate premium and member areas. Operating hours, table availability and access conditions are regulated and can change, making the current Crown casino information more reliable than historical floor counts.",
        "The dining offer is one of the complex's main strengths, from fine restaurants to casual food and quick service. Crown Towers, Metropol and Promenade provide different hotel products, while spa, shopping, live sport, bars and event facilities make the destination useful for a full evening or overnight stay."
      ],
      "games_description": "Crown Melbourne offers slot machines, electronic games and live tables across public and premium areas. Current game availability, operating hours and member conditions should be checked directly with Crown.",
      "fun_facts": "The complex extends along Melbourne's Southbank and connects three hotels with casino, dining, retail and event spaces under one resort brand.",
      "seo_title": "Crown Melbourne Casino, Hotels & Dining Guide",
      "seo_description": "Plan Crown Melbourne with sourced casino, hotels, restaurants, entry, transport, parking, map, official links and FAQs.",
      "official_website_url": "https://www.crownmelbourne.com.au/",
      "street_address": "8 Whiteman Street, Southbank, VIC 3006, Australia",
      "phone": "+61 3 9292 8888",
      "verified_opened_on": "1997-05-08",
      "verified_operator_name": "Crown Melbourne Limited",
      "hotel_name": "Crown Towers, Crown Metropol and Crown Promenade",
      "always_open": false,
      "has_slots": true,
      "has_table_games": true,
      "has_sportsbook": false,
      "has_bingo": false,
      "key_facts": [
        {"label":"Southbank complex","value":"Since 1997"},
        {"label":"Minimum age","value":"18+"},
        {"label":"Hotels","value":"Three"},
        {"label":"Setting","value":"Yarra riverfront"}
      ],
      "editorial_highlights": [
        {"title":"Large connected complex","text":"Casino, three hotels, dining, bars, shops and events extend across a long Southbank footprint."},
        {"title":"Multiple gaming environments","text":"Public, electronic, table and premium areas serve different products and access conditions."},
        {"title":"Melbourne dining destination","text":"Fine restaurants, casual venues and late-night options make food a major part of the visit."}
      ],
      "visit_details": {
        "hours":"Casino and hospitality venues trade extended hours, but individual gaming areas, restaurants and services use separate schedules and special-day restrictions. Confirm current times directly with Crown.",
        "access":"Casino gaming is restricted to guests aged 18 or over. Accepted photo identification, entry rules and dress standards apply; review Crown's current conditions before visiting.",
        "getting_there":"Crown is at 8 Whiteman Street in Southbank, within walking distance of Southern Cross and Flinders Street areas and served by trams, taxis and ride services.",
        "parking":"A large paid car park and valet services are integrated with the complex. Rates, validation and event conditions vary."
      },
      "amenities": [
        {"category":"hotel","name":"Crown Towers, Metropol and Promenade","description":"Three connected hotels cover luxury, contemporary and practical city stays within the same complex."},
        {"category":"dining","name":"Fine, casual and quick dining","description":"The official restaurant directory spans premium venues, international cuisines, casual meals and fast options."},
        {"category":"entertainment","name":"Bars, shopping, spa and events","description":"Nightlife, wellness, retail, live programming and conference facilities broaden the Southbank resort."}
      ],
      "faqs": [
        {"question":"What is the minimum age at Crown Melbourne casino?","answer":"Guests must be at least 18 to enter gaming areas. Accepted photo identification may be required."},
        {"question":"Does Crown Melbourne have hotels?","answer":"Yes. Crown Towers, Crown Metropol and Crown Promenade are connected to the entertainment complex."},
        {"question":"Is the casino open 24 hours?","answer":"The complex trades extended hours, but gaming zones and services can have specific schedules and special-day restrictions. Check Crown's current information."},
        {"question":"Is parking available?","answer":"Yes. Crown operates a large paid car park and valet services, with rates and validation conditions that vary by visit."}
      ],
      "editorial_source_urls": [
        "https://www.crownmelbourne.com.au/",
        "https://www.crownmelbourne.com.au/restaurants",
        "https://www.crownmelbourne.com.au/plan-your-visit/contact-us"
      ]
    }
  ]
  $profiles$::jsonb) as p(
    id bigint,
    expected_name text,
    short_description text,
    summary text,
    editorial_title text,
    editorial_paragraphs jsonb,
    games_description text,
    fun_facts text,
    seo_title text,
    seo_description text,
    official_website_url text,
    street_address text,
    phone text,
    verified_opened_on date,
    verified_operator_name text,
    hotel_name text,
    always_open boolean,
    has_slots boolean,
    has_table_games boolean,
    has_sportsbook boolean,
    has_bingo boolean,
    key_facts jsonb,
    editorial_highlights jsonb,
    visit_details jsonb,
    amenities jsonb,
    faqs jsonb,
    editorial_source_urls jsonb
  )
), updated as (
  update public.casinos as c
  set
    short_description = p.short_description,
    summary = p.summary,
    description = p.summary,
    editorial_title = p.editorial_title,
    editorial_paragraphs = array(select jsonb_array_elements_text(p.editorial_paragraphs)),
    games_description = p.games_description,
    fun_facts = p.fun_facts,
    seo_title = p.seo_title,
    seo_description = p.seo_description,
    official_website_url = p.official_website_url,
    street_address = p.street_address,
    phone = p.phone,
    verified_opened_on = coalesce(p.verified_opened_on, c.verified_opened_on),
    verified_operator_name = p.verified_operator_name,
    owner_name = null,
    hotel_name = p.hotel_name,
    always_open = p.always_open,
    has_slots = p.has_slots,
    has_table_games = p.has_table_games,
    has_sportsbook = p.has_sportsbook,
    has_bingo = p.has_bingo,
    profile_version = 2,
    key_facts = p.key_facts,
    editorial_highlights = p.editorial_highlights,
    visit_details = p.visit_details,
    amenities = p.amenities,
    faqs = p.faqs,
    gallery_images = '[]'::jsonb,
    editorial_source_urls = array(select jsonb_array_elements_text(p.editorial_source_urls)),
    editorial_verified_at = '2026-08-03T18:33:57Z'::timestamptz,
    enrichment_status = 'verified_official',
    last_verified_at = '2026-08-03T18:33:57Z'::timestamptz,
    verified_fields = coalesce(c.verified_fields, '{}'::jsonb) || jsonb_build_object(
      'official_website_url', jsonb_build_object('value', p.official_website_url, 'source', 'official', 'source_url', p.editorial_source_urls ->> 0, 'verified_at', '2026-08-03T18:33:57Z'),
      'street_address', jsonb_build_object('value', p.street_address, 'source', 'official', 'source_url', p.editorial_source_urls ->> 0, 'verified_at', '2026-08-03T18:33:57Z'),
      'verified_operator_name', jsonb_build_object('value', p.verified_operator_name, 'source', 'official', 'source_url', p.editorial_source_urls ->> 0, 'verified_at', '2026-08-03T18:33:57Z'),
      'editorial_profile', jsonb_build_object('value', 2, 'source', 'official', 'source_url', p.editorial_source_urls ->> 0, 'verified_at', '2026-08-03T18:33:57Z')
    ),
    updated_at = now()
  from profiles as p
  where c.id = p.id and c.name = p.expected_name
  returning c.id
)
select count(*) from updated;

do $$
declare
  enriched_count integer;
begin
  select count(*) into enriched_count
  from public.casinos
  where profile_version = 2
    and id = any(array[1,242,2143,3088,3287,3524,3547,3558,3571,3636,3929,3931,4004,4023,4115,4270,4332,5535,5696,6159]::bigint[]);

  if enriched_count <> 20 then
    raise exception 'Expected 20 editorial profiles, found %', enriched_count;
  end if;
end
$$;
