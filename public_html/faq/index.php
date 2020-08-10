<?php
/**
 * Page to display Frequently Asked Questions
 * 
 * This page displays the Frequently Asked Questions.
 * Logged in users view this page.
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');

// turn on output buffering
ob_start('template');

// start a session
session_start();

// Test for authorized user
if (empty($_SESSION["id"])) {
    header("Location: /login/");
    exit;
}

//$debug = debug();

$page_title = "Frequently Asked Questions";
$javascript = '';

?>
{header}
{main_nav}

    <main role="main" id="faq_page">
        <h1 class="mb-5 sticky-top">FAQ</h1>
        <section class="accordion" id="faq_overview">
            <h2 id="h1" aria-controls="c1" data-target="#c1" data-toggle="collapse" aria-expanded="true">What's the deal?</h2>
            <p id="c1" aria-labelledby="h1" class="collapse show" data-parent="#faq_overview">The objective of the Challenge is to earn the highest purse/winnings for one day at the racetrack.</p>
            
            <h2 id="h2" aria-controls="c2" data-target="#c2" data-toggle="collapse" aria-expanded="false">How does it all work?</h2>
            <ul id="c2" aria-labelledby="h2" class="collapse" data-parent="#faq_overview">
                <li>Each participant chooses one horse per race and whether they want to bet on that horse to WIN, PLACE or SHOW.</li>
                <li>The amount of winnings earned by the participant each reace is determined by the outcome of each race as displayed on the "tote board" located in the infield of the racetrack.</li>
                <li>The winnings from all races for the day are added together for each participant to determine the winners.</li>
            </ul>
            
            <h2 id="h3" aria-controls="c3" data-target="#c3" data-toggle="collapse" aria-expanded="false">What is the buy-in?</h2>
            <p id="c3" aria-labelledby="h3" class="collapse" data-parent="#faq_overview">Each participant must contribute $20.00 to the "Challenge Jackpot" prior to the start of the first race</p>

            <h2 id="h4" aria-controls="c4" data-target="#c4" data-toggle="collapse" aria-expanded="false">What's in it for me?</h2>
            <div id="c4" aria-labelledby="h4" class="collapse" data-parent="#faq_overview">
                <p><mark>Participants receive a variety of benefits &mdash; there's something for everyone!</mark></p> 
                <p>First place will receive:</p>
                <ul>
                    <li>50% of the Challenge Jackpot</li>
                    <li>Their name and winning total permanently added to the Challenge Winners Trophy</li>
                    <li>Posession of the Challenge Winners Trophy for an entire year</li>
                    <li>An official HOF Challenge Lapel Pin to be proudly worn at all forthcoming Challenge outings</li>
                </ul>
                <p>Second place will receive 30% of the Jackpot.</p>
                <p>Third place will receive 20% of the Jackpot.</p>
                <p>Last place will receive:</p>
                <ul>
                    <li>Their name and losing total permanetly added to the "Horses Arse" trophy</li>
                    <li>Posession of the "Horses Arse" trophy for an entire year</li>
                </ul>
                <p>All non-placing players still get to have a great day at the races!</p>
            </div>

            <h2 id="h5" aria-controls="c5" data-target="#c5" data-toggle="collapse" aria-expanded="false">What are the three types of horse track bets?</h2>
            <ul id="c5" aria-labelledby="h5" class="collapse" data-parent="#faq_overview">
                <li>Win: Choose a horse to finish in first place.</li>
                <li>Place: Choose a horse to finish in first OR second place.</li>
                <li>Show: Choose a horse to finish in first, second OR third place.</li>
            </ul>

            <h2 id="h6" aria-controls="c6" data-target="#c6" data-toggle="collapse" aria-expanded="false">What are odds and how is payout calculated?</h2>
            <ul id="c6" aria-labelledby="h6" class="collapse" data-parent="#faq_overview">
                <li>The more bets on a HORSE, the lower its ODDS...</li>
                <li>The lower the ODDS, the better the ODDS...</li>
                <li>The better the ODDS, the more likely the horse to WIN...</li>
                <li>The more likely the horse to WIN, the smaller the PAYOUT...</li>
            </ul>

            <h2 id="h7" aria-controls="c7" data-target="#c7" data-toggle="collapse" aria-expanded="false">Approximate Payouts</h2>
            <div id="c7" aria-labelledby="h7" class="collapse row justify-content-center" data-parent="#faq_overview">
                <table class="table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Odds</th>
                            <th scope="col">$2 Payoff</th>
                            <th scope="col">Odds</th>
                            <th scope="col">$2 Payoff</th>
                        </tr>  
                    </thead> 
                    <tbody>
                        <tr>
                            <td>1-5</td>
                            <td>$2.40</td>
                            <td>4-1</td>
                            <td>$10.00</td>
                        </tr>
                        <tr>
                            <td>2-5</td>
                            <td>$2.80</td>
                            <td>9-2</td>
                            <td>$11.00</td>
                        </tr>
                        <tr>
                            <td>1-2</td>
                            <td>$3.00</td>
                            <td>5-1</td>
                            <td>$12.00</td>
                        </tr>
                        <tr>
                            <td>3-5</td>
                            <td>$3.20</td>
                            <td>6-1</td>
                            <td>$14.00</td>
                        </tr>
                        <tr>
                            <td>4-5</td>
                            <td>$3.60</td>
                            <td>7-1</td>
                            <td>$16.00</td>
                        </tr>
                        <tr>
                            <td>1-1</td>
                            <td>$4.00</td>
                            <td>8-1</td>
                            <td>$18.00</td>
                        </tr>
                        <tr>
                            <td>6-5</td>
                            <td>$4.40</td>
                            <td>9-1</td>
                            <td>$20.00</td>
                        </tr>
                        <tr>
                            <td>7-5</td>
                            <td>$4.80</td>
                            <td>10-1</td>
                            <td>$22.00</td>
                        </tr>
                        <tr>
                            <td>3-2</td>
                            <td>$5.00</td>
                            <td>11-1</td>
                            <td>$24.00</td>
                        </tr>
                        <tr>
                            <td>8-5</td>
                            <td>$5.20</td>
                            <td>12-1</td>
                            <td>$26.00</td>
                        </tr>
                        <tr>
                            <td>9-5</td>
                            <td>$5.60</td>
                            <td>13-1</td>
                            <td>$28.00</td>
                        </tr>
                        <tr>
                            <td>2-1</td>
                            <td>$6.00</td>
                            <td>14-1</td>
                            <td>$30.00</td>
                        </tr>
                        <tr>
                            <td>5-2</td>
                            <td>$7.00</td>
                            <td>15-1</td>
                            <td>$32.00</td>
                        </tr>
                        <tr>
                            <td>3-1</td>
                            <td>$8.00</td>
                            <td>16-1</td>
                            <td>$34.00</td>
                        </tr>
                        <tr>
                            <td>7-2</td>
                            <td>$9.00</td>
                            <td>20-1</td>
                            <td>$42.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h2 id="h8" aria-controls="c8" data-target="#c8" data-toggle="collapse" aria-expanded="false">What are the biggest mistakes of horse handicapping? </h2>
            <blockquote id="c8" aria-labelledby="h8" class="collapse" data-parent="#faq_overview" >
                <h3 id="h9" aria-controls="c9" data-target="#c9" data-toggle="collapse" aria-expanded="true">Mistake #1: <span class="text-muted">It's important to pick winners</span></h3>
                <div id="c9" aria-labelledby="h9" class="collapse show" data-parent="#c8" >
                    <p><mark>Do NOT fall prey to the most common, yet immaterial and irrelevant, question: "Who do you like?" A far more significant question is, "Who should I bet?".</mark>
                    <p>Always come to the track to "make money": never come to the track "to pick horses to WIN" because: </p>
                    <ul>
                        <li>Horses who WIN, only win 1/3 of the time.</li>
                        <li>The average payoff for Horses who WIN is NOT high enough for betters to earn a profit.</li>
                        <li>Any time you bet on the most likely horse to WIN without considering if you are investing in a fair return on your investment, you are getting the worst of it.</li>
                    </ul>
                    <p>The correct approach to betting on horses &mdash; get the best of it and have the odds work in your favor. Remember: you are not trying to pick the winner but make a good bet (i.e. attractive payback).</p>
                    <p>By eliminating horses, especially the heavily bet horses, including, when possible, the favorite, you are presented with more wagering options </p>
                    <p>If you believe the favorite is not worth the price &mdash; and they lose twice as often as they win:</p>
                    <ul>
                        <li>you have more leeway</li>
                        <li>the race has more good bets</li>
                        <li>you automatically get better value</li>
                    </ul>
                    <p>By being able to figure out who will NOT win, you do NOT have to be as concerned with who will win. Always try to eliminate the "reputation horses": those animals whose odds are more reflective of some distant accomplishment than their recent form or their realistic chances of winning that day's race.</p>
                    <p>The biggest edge you have is playing against a public that does not understand how to work the odds.</p>
                    <ul>
                        <li>Example: if the weather has only a 10 percent chance of rain tomorrow, the "honest odds" you should receive on it raining tomorrow should be 9/1. Therefore, you should not bet it will rain tomorrow with only 6/1 odds. However, if you were offered 20/1 odds to bet it will rain tomorrow, that does not change the 10 percent chance of rain tomorrow, but it does offer you an attractive payback.</li>
                    </ul>
                    <p>The bottom line? "Don't bet a pick. Pick a bet." </p>
                </div>

                <h3 id="h10" aria-controls="c10" data-target="#c10" data-toggle="collapse" aria-expanded="false">Mistake #2: <span class="text-muted">Never let the tote board influence your betting decisions</span></h3>
                <div id="c10" aria-labelledby="h10" class="collapse" data-parent="#c8">
                    <p><mark>Allowing the tote board to influence your betting decisions is one of the most fundamental aspects of betting. If you cannot grasp this concept you really have no chance of winning and should be doing something else.</mark></p>
                    <p><u>Key Betting Element:</u> the <i>only</i> way to determine a horse's value is to compare a horse's realistic chances of winning to his ACTUAL ODDS vs LISTED ODDS.</p>
                    <p>If you take what the board gives you, go for the value. You will have a much better chance of winning.</p>
                    <p>Sometimes watching the tote board can tell you when to bet horses that are lower in price than they should be.</p>
                    <p>Some people have more information than others. However, they:</p>
                    <ul>
                        <li>do NOT know it all and are always looking to learn.</li>
                        <li>are alert to picking up on information.</li>
                        <li>know there are scenarios where somebody else's information can work to their own advantage.</li>
                        <li>know the board can be a source of that information.</li>
                        <li>know the more unknowns and variables there are in a race, the more possible edges there are, too.</li>  
                        <li>remain alert and can read the board, to maximize their information.</li>
                    </ul>
                    <p>Everybody comes to the track with money and egos. Do not be the person who believes they know everything, and the board cannot tell them a thing. Those types are hopeless.</p>
                    <p>The important things are:</p>
                    <ul>
                        <li>understand the concept of value.</li>
                        <li>know how to read the board.</li>
                        <li>do not pick the best horse, make the best wager possible.</li>
                    </ul>
                </div>
            
                <h3 id="h11" aria-controls="c11" data-target="#c11" data-toggle="collapse" aria-expanded="false">Mistake #3: <span class="text-muted">Class will always tell</span></h3>
                <div id="c11" aria-labelledby="h11" class="collapse" data-parent="#c8">
                    <p><mark>When it comes to evaluating horses, there is no such thing as class.</mark> Do NOT let anyone tell you a horse cannot win because he does not have enough class.</p>
                    <p>If a horse is fast enough, and the situation is right, he can win.</p>
                    <p>Those who are foolish enough to believe in class do the sharp better a tremendous service.</p>
                    <p>The greatest thing about class are the uninformed folks out there who think it means something and often:</p>
                    <ul>
                        <li>overlook horses with legitimate chances to win.</li>
                        <li>bet horses who have no shot.</li>
                        <li>create true value for the smart better.</li>
                    </ul>
                    
                    <p>These days, speed figures tell you who the fastest horses are. Generally, the fastest horse's race in the best races, so nowadays, if insist on talking about class, you are just talking about speed.</p>
                    <p>The entire game is based on speed and money. Trainers:</p>
                    <ul>
                        <li>run their horses where they think they can win.</li>
                        <li>pick their spots based on how fast they think their horses can run.</li>
                    </ul>
                    <p>Obviously, the quality of a race is determined by the quality of the horses that compete in that race so designating a race as a Grade 1, months before you know the field, is ridiculous.</p>
                    <p>It is nonsense to select a horse based on how many Grade 1 races that horse has won. Do NOT do something that stupid. How a horse might have performed months or years ago is worthless.</p>
                    <p>The most important thing is the current form of the horse.</p>
                    <p>Even in Daily Racing Forms, which should know better, you can read ridiculous statements like, "faced better," or "first time in claimers." When they load a horse into the gate, he does not look around and say, "I raced against better horses so I should beat these," or, "These are just a bunch of claimers."</p>
                    <p>Being in a claimer does not mean anything. Sometimes claiming races can be stronger than allowance races.</p>
                    <p>Do NOT make "absolute rule" or set up rigid guidelines for a series of races because:</p>
                    <ul>
                        <li>people will try to tell you that you should never do this in some situation or that you should always do that in another.</li>
                        <li>every race is a separate puzzle with its own clues.</li>
                    </ul>
                </div>

                <h3 id="h12" aria-controls="c12" data-target="#c12" data-toggle="collapse" aria-expanded="false">Mistake #4: <span class="text-muted">Public opinion can help you win</span></h3>
                <div id="c12" aria-labelledby="h12" class="collapse" data-parent="#c8">
                    <p>Those who can, do. Those who cannot, make selections in newspapers or via a telephone service, hold seminars or write books. <mark>The key ingredient to winning &mdash; and even a beginner should know this &mdash; is value.</mark> Any time you select in advance, without knowing the odds of the horses, you are not getting the best of it. It is impossible for anyone to win under those conditions.</p>
                    <p>You do not only have to know which horse to bet, but how to bet him:</p>
                    <ul>
                        <li>You've got to be able to look at pools and determine whether to bet straight or in exotics.</li>
                        <li>You certainly cannot do that if you are handing out selections in a paper or over a telephone.</li>
                    </ul>
                    <p>Many handicappers, public and private, make their first mistake before they ever get to the racetrack. Do not be the person who arrives at the track with a program full of horses' names with lines drawn through them. This is a hopeless strategy.</p>
                    <p>You cannot learn how to handicap horses from a book, but:</p>
                    <ul>
                        <li>What you will learn from a book is a set of rules or tendencies. All that is nonsense.</li>
                        <li>You will learn to bet certain horses in certain situations and not to bet other horses in certain situations.</li>
                        <li>This is a total waste of time because not only do tendencies change but all that is related to odds/price.</li>
                    </ul>
                    <p>As long as the public relies on selections made in advance, without the benefit of the latest information and on the advice of guys who would go broke if they played their own picks, the educated and intelligent better has an enormous edge.</p>
                    <p>Remember: people who make their picks in a newspaper, host seminars, or have a 900 number, know everything there is to know about racing except how to win. If they did, they would be doing it themselves.</p>
                </div>

                <h3 id="h13" aria-controls="c13" data-target="#c13" data-toggle="collapse" aria-expanded="false">Mistake #5: <span class="text-muted">There are many tried and true horse betting rules</span></h3>
                <div id="c13" aria-labelledby="h13" class="collapse" data-parent="#c8"> 
                    <p><mark>No professional bettor &mdash; no one with any real knowledge of the game &mdash; would buy into "tried an true rules."</mark></p>
                    <p>Nor would they make such ridiculous and meaningless statements as:</p>
                    <ul>
                        <li>"Never bet a horse to do something he hasn't done before."</li>
                        <li>"Never bet a 3-year-old against older horses in the spring."</li>
                        <li>"Never bet fillies against colts."</li>
                        <li>"Never bet maidens versus winners."</li>
                        <li>"Never bet the highest weighted horse on a muddy track."</li>
                    </ul>
                    <p>At the highest levels of the game, among those who win, this stuff is laughable. Such arbitrary "rules" can become an asset for astute bettors.</p>
                </div>
            </blockquote>
        </section>

      
    </main>
    {footer}
<?php ob_end_flush(); ?>
