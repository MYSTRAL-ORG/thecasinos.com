@extends('layout')
@section('page_title', 'TheCasinos.com : Online reference to Onsite experience')


@php
    use Illuminate\Support\Str;
@endphp
@section('category')
	<div class="container feuille-container">
		<section class=" feuille">
			<h2>List of Casinos in {{ $country}}</h2>
			<p>Explore our curated list of premier casinos across the USA. Dive into the world of entertainment and luxury, all in one place. From the bustling streets of Las Vegas to the serene resorts of Atlantic City, discover the best gaming destinations. Let the adventure begin and experience the thrill of American casinos!</p>
			<div class="">

                <div  class="row" >
                @foreach($casinos as $casino)

                        <div class="col-lg-4 col-md-6 col-sm-12 casino-box">
                            <div class="casino-image">
                                <img src="{{env('APP_URL').'/img/casino/'.$casino->img_url  }}" alt="{{$casino->name}} Casino">
                                <div class="category-location">
                                    <img src="{{ env('APP_URL') }}/img/icons/location.png" alt="Location Icon">
                                    <span>{{$casino->city_name}}</span>
                                </div>
                            </div>
                            <div class="casino-info">
                                <h3 class="two-lines">{{$casino->name}} </h3>
                                <p class="three-lines">{{ Str::limit($casino->resume_1_line, 80) }}</p>

                                <a class="button" href="{{ route('casino', ['country' => $casino->country_title  ,'city' => $casino->city_title , 'name' => $casino->slug] )}}">View casino</a>
                            </div>
                        </div>

              @endforeach
                </div>
			</div>

			<div class="pagination-container">
				<div class="pagination">

                    <a  class="button" href="{{$casinos->previousPageUrl()}}">Précédente</a>
					<a  class="button" href="{{$casinos->nextPageUrl()}}">Suivante</a>
				</div>
			</div>
			<h2>About Casinos in the USA</h2>
			<p>Hey there, fellow traveler on the World Wide Web! Have you ever thought about the allure of land-based casinos in the USA? Why is it that the bright lights, the sound of coins clinking, and the thrill of the game captivate so many of us? Let's dive into this glittering world and explore the top gambling states in the US. Ready to embark on this virtual road trip?</p>
			<h3>Why are Land-Based Casinos So Enticing?</h3>
			<p>Imagine this: you're stepping into a grand hall with opulent chandeliers, the murmur of chatter, and the background tunes of slot machines. Doesn't that paint a picture? Land-based casinos are like theme parks for adults. They're not just about gambling; they offer an escape, a place where you can forget your daily grind, even if for a few hours. And guess what? The USA is home to some of the most iconic ones!</p>
			<h3>Which States are Rolling the Highest Dice?</h3>
			<p>Now, onto the real deal. Which states have truly embraced the casino culture? Let's roll the dice:</p>
			<ol>
			  <li >
			    <p><strong>Nevada:</strong> Could we even start without mentioning Las Vegas? Sin City is synonymous with gambling. From the legendary Bellagio to the historic Golden Nugget, this is THE place to be.</p>
		      </li>
			  <li>
			    <p><strong>New Jersey:</strong> Ah, Atlantic City – where the Boardwalk Empire reigns. Offering a blend of beach vibes and high-stake games, it's no wonder folks flock here!</p>
		      </li>
			  <li>
			    <p><strong>Pennsylvania:</strong> A dark horse in the race, but don't let that fool you. With its casinos offering a mix of horse racing and gambling, it&rsquo;s a double whammy of excitement.</p>
		      </li>
			  <li>
			    <p><strong>Louisiana:</strong> The Big Easy isn&rsquo;t just about jazz and beignets. New Orleans has its fair share of glitzy casinos that meld perfectly with its vibrant nightlife.</p>
		      </li>
			  <li>
			    <p><strong>California:</strong> Surprised? Native American casinos have flourished here, giving the Golden State its own touch of gaming gold.</p>
		      </li>
		  </ol>
		  <p>Ever wonder why these states stand out? Could it be the rich history, the dazzling shows, or the promise of a jackpot win? Maybe it's a mix of it all.</p>
		<h3>Rolling Forward</h3>
		<p>Now, with online casinos burgeoning, will the charm of these land-based behemoths wane? It's like comparing an eBook to a vintage hardcover. Each has its own charm. But one thing's for sure: the allure of a tactile game, the feel of real chips, and the human interactions – they're irreplaceable.</p>
		<p>So, the next time you're itching for an adventure, why not consider a visit to one of these iconic gambling states? Who knows, Lady Luck might just be waiting around the corner!</p>
		<p>Absolutely! Let's delve deeper into the captivating world of gambling in the USA, covering some facts, the intricate tapestry of laws, and a brief stroll down memory lane.</p>
		<h3>Gambling Facts: Did You Know?</h3>
		<p>Let's sprinkle in some trivia, shall we?</p>
		<ul>
		  <li>
			<strong>The Biggest Single Win</strong>: Ever dreamt of winning big? In 2003, a 25-year-old software engineer did just that, landing a staggering $39.7 million at the Excalibur Casino in Las Vegas. Talk about a jackpot!
		  </li>
		  <li>
			<strong>Slot Machines Rule</strong>: While poker and blackjack tables get a lot of attention in movies, slot machines account for about 70% of a casino's income. Sounds like a lot of spinning, right?<
		  </li>
		  <li>
			<strong>Not Just About Money</strong>: Casinos are designed to keep you inside and engaged. From the lack of clocks and windows to the strategic maze-like layouts, it&rsquo;s all about the experience. Ever noticed that?
		  </li>
		</ul>
		<h3>Gambling Law: Navigating the Legal Landscape</h3>
		<p>Ah, the rulebook. It's not all fun and games without some guidelines:</p>
		<ul>
		  <li>
			<strong>The Interstate Wire Act</strong>: Established in 1961, this law was initially created to curb organized crime by targeting bookies and casinos that used wired communications for betting. Nowadays, it mainly pertains to online gambling.
		  </li>
		  <li>
			<strong>Indian Gaming Regulatory Act (IGRA) of 1988</strong>: Recognize those Native American casinos in California we chatted about earlier? This act allows tribes to operate casinos on their reservations, as long as the state they're in permits such gaming.
		  </li>
		  <li>
			<strong>The Unlawful Internet Gambling Enforcement Act (UIGEA) of 2006</strong>: While online gambling is a bit of a gray area, this act cracks down on financial institutions processing transactions related to online betting.
		  </li>
		</ul>
		<h3>A Glimpse into Gambling History</h3>
		<p>Now, for a quick trip in our time machine:</p>
		<ul>
		  <li>
			<strong>Colonial Beginnings</strong>: Gambling in the US dates back to colonial times. Early settlers had different attitudes towards gambling, with the Puritans in Massachusetts banning it, while the English colonists in Virginia embraced it.
		  </li>
		  <li>
			<strong>The Gold Rush</strong>: The 1800s saw gambling explode in saloons during the California Gold Rush. It was the wild, wild west in more ways than one!
		  </li>
		  <li>
			<strong>Rise of Las Vegas</strong>: The early 20th century saw gambling bans across states. However, Nevada legalized it in 1931, paving the way for Las Vegas to become the world's gambling capital.
		  </li>
		  <li>
			<strong>Atlantic City Joins In</strong>: Not to be outdone, New Jersey permitted casinos in 1978, with Atlantic City blossoming as the East Coast's gambling hub.
		  </li>
		</ul>
			<h3>Let's try online :</h3>
			@php
				$lines = 3; // Nombre de lignes à afficher par défaut
				$columns = ['Logo', 'Brand', 'Bonus', 'Note', 'Review', 'Casino']; // Les colonnes à afficher

			@endphp
            @include("top10");
		</section>
	</div>
@endsection
