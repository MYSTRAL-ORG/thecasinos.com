with pt as (
    SELECT ID AS GID,
           DATA ->> 'id' AS ID_SOURCE,
           DATA ->> 'name',
           DATA ->> 'opened' AS OPENED,
           DATA ->> 'table_games' AS TABLE_GAMES,
           ST_SETSRID(ST_MAKEPOINT((DATA -> 'location' ->> 'longitude')::numeric, (DATA -> 'location' ->> 'latitude')::numeric),4326) AS GEOM
    FROM SOURCE_CASINOS
    WHERE DATA -> 'location' ->> 'longitude' is not NULL and  DATA -> 'location' ->> 'longitude' <> ''
      AND DATA -> 'location' ->> 'latitude' is not  NULL and  DATA -> 'location' ->> 'latitude' <> ''
)
select GID st_asgeojson(st_union(geom) ) as geojson
from pt;

Create extension postgis;


SELECT ID AS GID,
       DATA ->> 'id' AS ID_SOURCE,
       DATA ->> 'name',
       DATA ->> 'opened' AS OPENED,
       DATA ->> 'table_games' AS TABLE_GAMES,
       ST_SETSRID(ST_MAKEPOINT((DATA -> 'location' ->> 'longitude')::numeric, (DATA -> 'location' ->> 'latitude')::numeric),4326) AS GEOM
FROM SOURCE_CASINOS
WHERE DATA -> 'location' ->> 'longitude' is not NULL and  DATA -> 'location' ->> 'longitude' <> ''
  AND DATA -> 'location' ->> 'latitude' is not  NULL and  DATA -> 'location' ->> 'latitude' <> '' ;


SELECT ID AS GID, DATA ->> 'id' AS ID_SOURCE, DATA ->> 'name', DATA ->> 'opened' AS OPENED ,DATA ->> 'table_games' AS TABLE_GAMES,ST_SETSRID(ST_MAKEPOINT((DATA -> 'location' ->> 'longitude')::numeric, (DATA -> 'location' ->>'latitude')::numeric),4326) AS GEOM FROM SOURCE_CASINOS WHERE DATA -> 'location' ->> 'longitude' is not NULL and  DATA -> 'location' ->> 'longitude' <> '' AND DATA -> 'location' ->> 'latitude' is not  NULL and  DATA -> 'location' ->>'latitude' <> ''







--create table
create table casino   as
SELECT
    (row_number() over())::int id,
    DATA ->> 'id' AS ID_SOURCE,
    DATA ->> 'name' as name,
    DATA ->> 'slug' as slug,
    to_date( DATA ->> 'opened','YYYY-MM-DD'::text ) AS opened,
    --table_machine
    (DATA ->> 'gaming_machines')::integer AS gaming_machines,
    (DATA ->> 'poker_tables')::integer AS poker_tables,
    (DATA ->> 'casino_square_footage')::integer as square_footage,
    (DATA -> 'location' -> 'country' ->>'name')::text as country_name ,
    (DATA -> 'location' -> 'country' ->>'title')::text as country_title,
    (DATA -> 'location' -> 'country' ->>'iso')::text as country_iso_code,
    (DATA -> 'location' ->  'state' ->> 'name')::text as state_name ,
    (DATA -> 'location' ->  'state' ->> 'title')::text as state_title ,
    (DATA -> 'location' ->  'city' ->> 'name')::text as city_name ,
    (DATA -> 'location' ->  'city' ->> 'title')::text as city_title ,
    case when  (DATA -> 'location'   ->> 'longitude') = '' THEN null  else (DATA -> 'location'   ->> 'longitude')::numeric end  as location_longitude ,
    case when  (DATA -> 'location'   ->> 'latitude') = '' THEN null  else (DATA -> 'location'   ->> 'latitude')::numeric end location_latitude ,
    CAST(DATA -> 'features' ->> 'restaurants'AS BOOLEAN) as restaurants ,
    CAST(DATA -> 'features' ->> 'venues' AS BOOLEAN) as venues ,
    CAST(DATA -> 'features' ->> 'hotels' AS BOOLEAN) as hotels,
    CAST(DATA -> 'features' ->> 'shops' AS BOOLEAN) as shops,
    CAST(DATA -> 'features' ->> 'spas' AS BOOLEAN) as spas,

    CAST(DATA -> 'game_categories' ->> 'casino' AS BOOLEAN) as cat_casino,
    CAST(DATA -> 'game_categories' ->> 'poker' AS BOOLEAN) as cat_poker,
    CAST(DATA -> 'game_categories' ->> 'sportsbook' AS BOOLEAN) as cat_sportsbook,
    CAST(DATA -> 'game_categories' ->> 'horseracing' AS BOOLEAN) as cat_horseracing,
    CAST(DATA -> 'game_categories' ->> 'simulcasting' AS BOOLEAN) as cat_simulcasting,
    CAST(DATA -> 'game_categories' ->> 'offTrack' AS BOOLEAN) as cat_offTrack,
    CAST(DATA -> 'game_categories' ->> 'greyhounds' AS BOOLEAN) as cat_greyhounds,
    CAST(DATA -> 'game_categories' ->> 'jaialai' AS BOOLEAN) as jaialai,
    CAST(DATA -> 'game_categories' ->> 'bingo' AS BOOLEAN) as cat_bingo,
    CAST(DATA -> 'game_categories' ->> 'slotMachines' AS BOOLEAN) as cat_slotMachines,
    CAST(DATA -> 'game_categories' ->> 'tableGames' AS BOOLEAN) as cat_tableGames
FROM SOURCE_CASINOS

         update casino  c   set img_url = concat ( DATA -> 'images' ->'Headline'  ->0 ->>'id', '_', DATA -> 'images' ->'Headline'  ->0 ->>'filename','.', DATA -> 'images' ->'Headline'  ->0 ->>'ext')
 from SOURCE_CASINOS s

 where  c.id_source = s.DATA ->> 'id'
