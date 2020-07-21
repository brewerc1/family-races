<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
$page_title = "Frequently Asked Questions";
$javascript = <<<HERE
HERE;
// turn on output buffering 
ob_start('template');
session_start();
?>
{header}
{main_nav}

    <main role="main">
        <h2>What's the deal?</h2>
            <p>The objective of the Challenge is to earn the highest purse/winnings for one day at the racetrack</p>
        
        <h2>How does it all work?</h2>
            <ul>
                <li>Each participant chooses one horse per race and whether they want to bet on that horse to WIN, PLACE or SHOW.</li>
                <li>The amount of winnings earned by the participant each reace is determined by the outcome of each race as displayed on the "tote board" located in the infield of the racetrack.</li>
                <li>The winnings from all races for the day are added together for each participant to determine the winners.</li>
            </ul>
            
        <h2>What is the buy&mdash;in?</h2>
            <p>Each participant must contribute $20.00 to the "Challenge Jackpot" prior to the start of the first race</p>

        <h2>What's in it for me?</h2>
            <ul>
                <li>First place will receive:</li>
                <ul>
                    <li>50% of the Challenge Jackpot</li>
                    <li>Their name and winning total permanently added to the Challenge Winners Trophy</li>
                    <li>Posession of the Challenge Winners Trophy for an entire year</li>
                    <li>An official HOF Challenge Lapel Pin to be proudly worn at all forthcoming Challenge outings</li>
                </ul>
                <li>Second place will receive 30% of the Jackpot.</li>
                <li>Third place will receive 20% of the Jackpot.</li>
                <li>Last place will receive:</li>
                <ul>
                    <li>Their name and losing total permanetly added to the "Horses Arse" trophy</li>
                    <li>Posession of the "Horses Arse" trophy for an entire year</li>
                </ul>
                <li>All non&mdash;placing players still get to have a great day at the races!</li>
            </ul>

        <h2>What are the three types of bets at the horse track?</h2>
            <ul>
                <li>Win: Choose a horse to finish in first place.</li>
                <li>Place: Choose a horse to finish in first OR second place.</li>
                <li>Show: Choose a horse to finish in first, second OR third place.</li>
            </ul>

        <h2>What are the odds and how are they used to calculate the payout on bets at the horse track?</h2>
            <ul>
                <li>The more bets on a HORSE, the lower its ODDS...</li>
                <li>The lower the ODDS, the better the ODDS...</li>
                <li>The better the ODDS, the more likely the horse to WIN...</li>
                <li>The more likely the horse to WIN, the smaller the PAYOUT...</li>
            </ul>

        <section>
        <h2>Approximate Payouts</h2>
            <table>
                <tr>
                    <th>Odds</th>
                    <th>$2 Payoff</th>
                    <th>Odds</th>
                    <th>$2 Payoff</th>
                </tr>   
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
            </table>
    </section>
    <section>
    <h2>What are the biggest mistakes of horse handicapping? </h2>

    <h3>Handicapping Mistake #1: It’s important to pick winners</h3>
        <ul>
            <li>Do NOT fall prey to the most common, yet immaterial and irrelevant, question: “Who do you like?” A far more significant question is, “Who should I bet?”.</li>
            <li>Always come to the track to “make money”: never come to the track “to pick horses to WIN” because: </li>
                <ul>
                    <li>Horses who WIN, only win 1/3 of the time.</li>
                    <li>The average payoff for Horses who WIN is NOT high enough for betters to earn a profit.</li>
                    <li>Any time you bet on the most likely horse to WIN without considering if you are investing in a fair return on your investment, you are getting the worst of it.</li>
                </ul>
            <li>The correct approach to betting on horses: get the best of it and have the odds work in your favor. Remember: you are not trying to pick the winner but make a good bet (i.e. attractive payback).</li>
            <li>By eliminating horses, especially the heavily bet horses, including, when possible, the favorite, you are presented with more wagering options </li>
            <li>If you believe the favorite is not worth the price &mdash; and they lose twice as often as they win:</li>
                <ul>
                    <li>you have more leeway</li>
                    <li>the race has more good bets</li>
                    <li>you automatically get better value</li>
                </ul>
            <li>By being able to figure out who will NOT win, you do NOT have to be as concerned with who will win. Always try to eliminate the “reputation horses,”: those animals whose odds are more reflective of some distant accomplishment than their recent form or their realistic chances of winning that day’s race.</li>
            <li>The biggest edge you have is playing against a public that does not understand how to work the odds.</li>
                <ul>
                    <li>Example: if the weather has only a 10 percent chance of rain tomorrow, the “honest odds” you should receive on it raining tomorrow should be 9/1. Therefore, you should not bet it will rain tomorrow with only 6/1 odds. However, if you were offered 20/1 odds to bet it will rain tomorrow, that does not change the 10 percent chance of rain tomorrow, but it does offer you an attractive payback.</li>
                </ul>
            <li>The bottom line? “Don’t bet a pick. Pick a bet.” </li>
        </ul>

    <h3>Mistake #2: “Never let the tote board influence your betting decisions”</h3>
        <ul>
            <li>Allowing the tote board to influence your betting decisions is one of the most fundamental aspects of betting. If you cannot grasp this concept you really have no chance of winning and should be doing something else.</li>
            <li>Key Betting Element: the ONLY way to determine a horse’s value is to compare a horse’s realistic chances of winning to his ACTUAL ODDS vs LISTED ODDS.</li>
            <li>If you take what the board gives you, go for the value. You will have a much better chance of winning.</li>
            <li>Sometimes watching the tote board can tell you when to bet horses that are lower in price than they should be.</li>
            <li>Some people have more information than others. However, they:</li>
                <ul>
                    <li>do NOT know it all and are always looking to learn.</li>
                    <li>are alert to picking up on information.</li>
                    <li>know there are scenarios where somebody else’s information can work to their own advantage.</li>
                    <li>know the board can be a source of that information.</li>
                    <li>know the more unknowns and variables there are in a race, the more possible edges there are, too.</li>  
                    <li>remain alert and can read the board, to maximize their information.</li>
                </ul>
            <li>Everybody comes to the track with money and egos. Do not be the person who believes they know everything, and the board cannot tell them a thing. Those types are hopeless.</li>
            <li>The important things are:</li>
                <ul>
                    <li>understand the concept of value.</li>
                    <li>know how to read the board.</li>
                    <li>do not pick the best horse, make the best wager possible.</li>
                </ul>
        </ul>
    
    <h3>Mistake #3: “Class will always tell”</h3>
        <ul>
            <li>When it comes to evaluating horses, there is no such thing as class.</li>
                <ul>
                    <li>Do NOT let anyone tell you a horse cannot win because he does not have enough class.</li>
                    <li>If a horse is fast enough, and the situation is right, he can win.</li>
                    <li>Those who are foolish enough to believe in class do the sharp better a tremendous service.</li>
                    <li>The greatest thing about class are the idiots out there who think it means something and often:</li>
                        <ul>
                            <li>overlook horses with legitimate chances to win.</li>
                            <li>bet horses who have no shot.</li>
                            <li>create true value for the smart better.</li>
                        </ul>
                </ul>
            <li>These days, speed figures tell you who the fastest horses are. Generally, the fastest horse’s race in the best races, so nowadays, if insist on talking about class, you are just talking about speed</li>
            <li>The entire game is based on speed and money. Trainers:</li>
                <ul>
                    <li>run their horses where they think they can win.</li>
                    <li>pick their spots based on how fast they think their horses can run.</li>
                </ul>
            <li>Obviously, the quality of a race is determined by the quality of the horses that compete in that race so designating a race as a Grade 1, months before you know the field, is ridiculous.</li>
            <li>It is nonsense to select a horse based on how many Grade 1 races that horse has won. Do NOT do something that stupid. How a horse might have performed months or years ago is worthless.</li>
            <li>The most important thing is the current form of the horse.</li>
            <li>Even in Daily Racing Forms, which should know better, you can read ridiculous statements like, “faced better,” or “first time in claimers.” When they load a horse into the gate, he does not look around and say, “I raced against better horses so I should beat these,” or, “These are just a bunch of claimers.”</li>
            <li>Being in a claimer does not mean anything. Sometimes claiming races can be stronger than allowance races.</li>
            <li>Do NOT make “absolute rules” or set up rigid guidelines for a series of races because.</li>
                <ul>
                    <li>people will try to tell you that you should never do this in some situation or that you should always do that in another.</li>
                    <li>every race is a separate puzzle with its own clues.</li>
                </ul>
        </ul>

    <h3>Mistake #4: “Public opinion can help you win”</h3>
        <ul>
            <li>Those who can, do. Those who cannot, make selections in newspapers or via a telephone service, hold seminars or write books.</li>
            <li>The key ingredient to winning, and even a moron should know this &mdash; is value. Any time you select in advance, without knowing the odds of the horses, you are not getting the best of it. It is impossible for anyone to win under those conditions.</li>
            <li>You do not only have to know which horse to bet, but how to bet him.</li>
                <ul>
                    <li>You have got to be able to look at pools and determine whether to bet straight or in exotics.</li>
                    <li>You certainly cannot do that if you are handing out selections in a paper or over a telephone.</li>
                </ul>
            <li>Many handicappers, public and private, make their first mistake before they ever get to the racetrack. Do not be the person who arrives at the track with a program full of horses’ names with lines drawn through them. This is a hopeless strategy.</li>
            <li>You cannot learn how to handicap horses from a book.</li>
                <ul>
                    <li>What you will learn from a book is a set of rules or tendencies. All that is nonsense.</li>
                    <li>You will learn to bet certain horses in certain situations and not to bet other horses in certain situations.</li>
                    <li>This is a total waste of time because not only do tendencies change but all that is related to odds/price.</li>
                </ul>
            <li>As long as the public relies on selections made in advance, without the benefit of the latest information and on the advice of guys who would go broke if they played their own picks, the educated and intelligent better has an enormous edge.</li>
            <li>Remember: people who make their picks in a newspaper, host seminars, or have a 900 number, know everything there is to know about racing except how to win. If they did, they would be doing it themselves.</li>
        </ul>

    <h3>Mistake #5: “There are many tried and true rules to use when betting on horses” </h3>
        <ul>
            <li>No professional bettor, no one with any real knowledge of the game, would make such ridiculous and meaningless statements as:</li>
                <ul>
                    <li>“Never bet a horse to do something he hasn’t done before.”</li>
                    <li>“Never bet a 3-year-old against older horses in the spring.”</li>
                    <li>“Never bet fillies against colts.”</li>
                    <li>“Never bet maidens versus winners.”</li>
                    <li>“Never bet the highest weighted horse on a muddy track.”</li>
               </ul>
            <li>At the highest levels of the game, among those who win, this stuff is laughable.</li>
            <li>No professional bettor would ever buy into such garbage rules.</li>
            <li>Such arbitrary “rules” can become an asset for astute bettors.</li>
        </ul>

    </section>

      
    </main>
    {footer}
<?php ob_end_flush(); ?>
