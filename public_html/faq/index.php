<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');
// Authentication  and Authorization System
ob_start();
session_start();

if (!isset($_SESSION["id"]) || $_SESSION["id"] == 0)
    header("Location: /login/");


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no"> 
    <title>Skeleton HTML</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
    <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">-->
    <link href="/css/races.css" rel="stylesheet">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        nav#main-navigation li {
            display: inline-block;
            width: 18%;
        }
        nav#main-navigation ul {
            margin:0;
            padding:0;
        }
    </style>
    </head>
     <body>
    <!--The main navigation menu to be displayed on most pages. Not all links work yet.-->
    <nav id="main-navigation">
        <h1>Main Navigation</h1>
        <ul>
            <li><a href="http://localhost/races/">Races</a></li>
            <li><a href="http://localhost/HOF/">HOF</a></li>
            <li><a href="http://localhost/faq/">FAQ</a></li>
            <li><a href="http://localhost/user/">Me</a></li>
            <?php
            if ($_SESSION['admin']) {
echo <<< ADMIN
<li><a href= "http://localhost/admin/">Admin</a></li>
ADMIN;
            }
            ?>
            <li><a href="http://localhost/logout">Log out</a></li>
        </ul>
    </nav>
    <main role="main">
        <h1>Types of Bets</h1>
        <ul>
            <li>Win: Choose a horse to finish in first place.</li>
            <li>Place: Choose a horse to finish in first OR second place.</li>
            <li>Show: Choose a horse to finish in first, second OR third place.</li>
        </ul>
        <div>
        <h1>Approximate Payouts</h1>
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
    </div>
    <div>
        <h1>Biggest Mistakes of Handicapping</h1>
        <p>    Avoid these misguided strategies. 

Previously, we examined the four pillars of horse handicapping – speed, pace, track bias and value – on which a foundation of successful horse race betting is based. Aided by a professional horse player who asked that his identity be protected, over the next few months we’ll take a closer look at some common handicapping mistakes, a series of misguided strategies and unproven theories that often doom the unsophisticated bettor. 
Handicapping Mistake #1: It’s important to pick winners 
Many people are beaten before they ever visit a racetrack, wager at a satellite facility or place a bet online. The reason is that they fall prey to the most common and yet immaterial question asked in a horse wagering setting: “Who do you like?” The far more significant question is, “Who should I bet?” 
“Actually, picking winners doesn’t mean anything,” explained a Las Vegas based professional player with an annual income derived from betting horses that reached well into six figures. “It’s irrelevant. If the object of the game is make money – and why else would you play–then the way to do that is not by picking winners. Since favorites win about a third of the time, the public picks 33 percent winners. But that does them no good because the average payoff isn’t high enough for them to earn a profit. 
“The whole concept of gambling is to get the best of it, have the odds work in your favor,” said the player. “Any time you bet on the most likely winner without considering whether you’re getting a fair price, you’re getting the worst of it. 
“Think of it this way, if the weather forecaster said there was only a 10 percent chance of precipitation the next day then the honest price that it would rain would be 9/1,” explained the player. “So if someone offered you 6/1 that it would rain, you wouldn’t take the bet. But what if someone offered you 20/1? While the overwhelming likelihood still is for fair skies, remember that you’re not trying to pick the winner but make a good bet, so 20/1 is an attractive wager. 
“The biggest edge I have is playing against a public that doesn’t have a clue,” continued the professional bettor. 
“Unless you’re betting a stone-cold longshot, a horse who has value whether he’s 25/1 or 40/1, the price makes all the difference in the world. Most guys without a mathematical background can’t understand that there can be a giant difference between 2/1 and 3/1.” 
The player went on to explain that by eliminating horses, especially heavily bet horses, including, when possible, the favorite, the bettor is presented with more wagering options. 
“If you can toss out a 4/1 shot because he should be 10/1, you begin to swing the odds in your favor. If you think the favorite isn’t worth the price – and they lose twice as often as they win – you have much more leeway on how to play the race. Let’s say the favorite has 30 percent of the pool. Even with the takeout, you immediately have an edge. And with the favorite out, the race rates to have a lot of good bets. Automatically, you’re getting better value so you can afford to play more exacta combinations. By being able to figure out who won’t win, you don’t have to be as concerned with who will win.” 
Professional bettors like to eliminate what they call “reputation horses,” those animals whose odds are more reflective of some distant accomplishment than their recent form or their realistic chances of winning that day’s race. 
“A good example of a reputation horse is Funny Cide,” said the player. “Off any set of speed figures, he’s no faster than any of the horses he’s running against. He’s about the same, which means he’ll win occasionally. But, because he won the Kentucky Derby and the Preakness and developed a strong following based on a compelling life story, he’s a fan favorite and is consistently over bet.” 
So what’s the bottom line? “Don’t bet a pick. Pick a bet.” </br>
Handicapping Mistake #2: Never let the tote board influence your wagering decisions 
“You couldn’t possibly make a dumber statement,” one of Nevada’s best professional horse bettors who asked that his name not be used. “This is one of the most fundamental aspects of betting. I don’t want to be cruel but if you can’t grasp this concept you really have no chance of winning and should be doing something else.” 
The player went on to explain that only by comparing a horse’s realistic chances of winning to his actual price could a bettor determine that horse’s value, a key element of betting. 
The player cited the 1993 Belmont Stakes, in which he was documented in a June 4 Daily Racing Form column, a week before the race (you can look it up), explaining the principle. 
“I said that the 1993 Belmont Stakes was a random race, that you should throw out Prairie Bayou because he’d be over bet, and that Sea Hero was too slow. I said that that there were three horses, Cherokee Run, Virginia Rapids and Colonial Affair, who, off their form, had a chance to win. Each should have been about 5/1. But Cherokee Run (4/1) and Virginia Rapids (9/2) were mild underlays and the eventual race winner, Colonial Affair, at almost 14/1, was a monstrous overlay. Do you need a roadmap to know which way to go? 
“That was one of the biggest overlays imaginable. It was a totally absurd price, insane. Colonial Affair was beaten less than two lengths by Virginia Rapids going a mile and an eighth in the Peter Pan and the Belmont, at a mile and a half, was a much better distance for him. He had the advantage of tactical speed against a bunch of plodders and was one of the best bred (Pleasant Colony out of the Nijinsky II mare, Snuggle) horses in the race. It was a totally ridiculous price. The horse should have been 5/1.” 
The player pointed out that he didn’t love Colonial Affair. What he loved was the price. 
“If Virginia Rapids had been 14/1 I would have bet him and I would have lost,” admitted the player. “But in the long run, if you take what the board gives you, go for the value, you’ll have a much better chance of winning.” 
Sometimes, warned the player, watching the tote board can tell you when to bet horses that are lower in price than they should be. 
“Obviously, some people have more information than others. The real sharps understand this, realize they don’t know it all, and are always looking to learn. They’re alert to picking up on information and they know the board can be a source of that information.” 
There are scenarios, claimed the professional, where somebody else’s information can work to your advantage. 
“In a lot of cases, maiden races for instance, somebody, maybe an owner or trainer, is going to know more about a specific horse than you do. Knowing that a horse is fast is a tremendous edge. But that isn’t necessarily a bad situation for you because the more unknowns and variables there are in a race, the more possible edges there are, too. If you’re alert and can read the board, you can maximize those edges. 
“Everybody comes to the track with money but some guys also bring along their egos. They know everything so the board can’t tell them a thing. These people are hopeless.” 
The player explained that there is a lot more to watching the board than just betting on horses that take money. 
“Not every horse that is bet down is going to win,” he conceded. “But it’s totally absurd to think that none of them will, either. It’s another factor to consider.” 
Betting these “hot horses is a separate and more complex matter. 
“If a horse is 20/1 and goes to 5/1, the idea is not to try to win but to find 10/1 or 15/1 overlays on that horse in exactas or some other pool. The important thing is not to pick the best horse but make the best wager possible. 
“I’m not the greatest handicapper in the world,” the player said, “but I win because I’m able to put all the principles together. I understand the concept of value, know how to read the board and I’m a good bettor.” </br>

Handicapping Mistake #3: Class will tell 
“Years ago, before there were reliable speed figures and when there were fewer horses and fewer racetracks, class might have meant something. Today, there’s no such thing as class,” said the professional horse player. “Before accurate speed figures were available, people would try to compare horses by looking at where and against whom they raced. They thought that the better horses would race in the higher classes. These days, speed figures tell you who the fastest horses are. Generally, the fastest horses race in the best races, so nowadays, if you’re talking about class, you’re really just talking about speed.” 
The player insisted that the proliferation of horses and racetracks has led to a dilution of the product, blurring the lines that once separated runners based on class. 
“The entire game is based on speed and money. Trainers run their horses where they think they can win. They pick their spots based on how fast they think their horses can run, not on any outdated class system. In the old days you could never risk serious money on a maiden winner moving up to allowance. Now, if your horse is fast enough, you can.” 
The whole class system, said the player, is arbitrary. 
Start right at the top with Grade 1 races and you can see the fallacy in the system. What have you got, half a dozen people who don’t know anything about betting sitting in a room somewhere deciding what stakes should be Grade 1? Aren’t these the same geniuses who made the Blue Grass Stakes a Grade 2 a couple of years ago? 
Obviously, the quality of a race is determined by the quality of the horses that compete in that race so designating a race as a Grade 1, months before you know the field, is ridiculous. Sure, based on history, the Belmont Stakes is a Grade 1. But that certainly wasn’t a Grade 1 field in the Belmont Stakes this year. 
What’s really stupid though is how many people believe in class. You have guys making selections based on how many Grade 1 races a horse won. That’s nonsense. Colonial Affair had never won a stakes of any kind before he won the 1993 Belmont. 
If a horse’s speed figure in, say, an allowance is fast enough to win a stakes then, in the right circumstances, he can win a stakes. The most important thing is current form. Things like “back class,” how a horse might have performed months or years ago, are worthless. 
Even in Daily Racing Form, which should know better, you read ridiculous statements like, “faced better,” or “first time in claimers.” When they load a horse into the gate, he doesn’t look around and say, “I raced against better horses so I should be able to beat these,” or, “These are just a bunch of claimers.” Besides, being in a claimer doesn’t mean anything. Claiming races sometimes are stronger than allowance races. An allowance can be weak, depending on the conditions. 
The player warned against making absolute rules or setting up rigid guidelines for a series of races. 
“The game is dynamic and changeable and you should guard against making blanket statements,” he said. “People will try to tell you that you should never do this in some situation or that you should always do that in another. But every race is a separate puzzle with its own clues. Sometimes, for instance, a horse may be fast enough to move from maiden to allowance or from allowance to stakes but there are other conditions – track bias, post position, the way the race sets up, odds – that may be working against him or make him a risky bet. All these factors must be considered, of course. 
“But don’t let anyone tell you a horse can’t win because he doesn’t have enough class. If he’s fast enough, and the situation is right, he can win.” 
Those who are foolish enough to believe in class, contended the betting professional, do the sharp player a tremendous service. 
The greatest thing about class is that there are plenty of idiots out there who think it means something. Because of that, they often overlook horses with legitimate chances to win and bet horses who have no shot. They can create true value. </br>

Handicapping Mistake #4: Public prognosticators can help you win. 
Those who can, do. Those who can’t, make selections in newspapers or via a telephone service, hold seminars or write books. 
That was the shared opinion of a pair of professional horse players who regard public race handicappers, speakers and authors with a mixture of contempt and amusement. 
“As in life, the whole concept of gambling is getting the best of it,” began a player we’ll call Art, a professional gambler for more than three decades, as he lapsed into a philosophical mode. “The key ingredient to winning – and even a moron should know this – is value. Any time you select in advance, without knowing the odds of the horses, you’re not getting the best of it. It’s impossible for anyone to win under those conditions.” 
“Ben,” another professional who admitted to betting $10,000-$20,000 a day at Nevada’s race books, agreed. 
“Unless the guy is picking a stone-cold longshot, a horse who has value whether he’s 25/1 or 40/1, the price makes all the difference in the world. Most guys without a mathematical background can’t understand that there can be a giant difference between 2/1 and 5/2. You don’t only have to know which horse to bet, but how to bet him. You’ve got to be able to look at pools and determine whether to bet straight or in exotics. Most people can’t do that. And you certainly can’t do that if you’re handing out selections in a paper or over a telephone.” 
Art believes many handicappers, public and private, make their first mistake before they ever get to the racetrack. 
“You’ll see people who have lines drawn through horses’ names on their programs. This is a hopeless strategy. True, a horse that’s 15/1 may be a total disregard. But that same horse may be an automatic play at 30/1.” 
Neither professional player thought there was much to be gained from seminars, lectures or books on handicapping. 
“You can’t learn how to handicap from a book,” maintained Art. “What you’ll get out of a book is a set of rules or tendencies. All that is nonsense. They’ll tell you to bet certain horses in certain situations and not to bet other horses in certain situations. This is a total waste of time because not only do tendencies change but all that is related to price, anyway.” 
“Talking about last meet’s bias, or even yesterday’s track bias is meaningless,” added Ben. “A bias can change during a day’s card. It’s simply impossible to anticipate with any certainty how the track is going to play.” 
“Besides, a bias is only worth something when you’re able to discover it before the public does,” warned Art. “Once the public finds a bias, say it’s frontrunners in routes, then the edge and the value is lost. What makes the game so incredibly dynamic is are all the variables – bias, price, betting patterns – you must consider before wagering. No one can predict those factors in advance. That’s why handicapping services are basically worthless.” 
Still, both players feel they owe a debt of gratitude, dubious though it may be, to public handicappers. 
“The biggest edge I have is that I’m playing against a public that doesn’t have a clue,” insisted Art. “So long as the public is relying on selections made in advance, without the benefit of the latest information, on the advice of guys who’d go broke if they played their own picks, I have an enormous edge.” 
“Guys who make picks in a newspaper, host seminars or have a 900 number know everything there is to know about racing except how to win,” said Art. “If they did, they’d be doing it. Look, I don’t know what anyone else does but I win. I don’t have to sell my picks. If I want to make money all I must do is bet ’em.” </br>

Handicapping Mistake #5: There are several tried and true rules of the game 
Such as… 
“Never bet a horse to do something he hasn’t done before” 
“Never bet a 3-year-old against older horses in the spring” 
“Never bet fillies against colts” 
“Never bet maidens versus winners” 
and “Never bet the highest weighted horse on a muddy track” 
…which always should be followed. 
“No professional bettor, no one with any real knowledge of the game, would make such ridiculous and meaningless statements,” insisted the player. “Only a total amateur would utter such nonsense. Maybe this stuff sounds logical to the neophyte or the loser but anyone who bets horses for a living realizes this is infantile, nursery school mentality. At the highest levels of the game, among those who win, this stuff is laughable. No professional bettor would ever buy into such garbage.” 
OK, now tell us what you really think. 
“Look, I’m not going to waste my time going over each one of these ‘rules’ one by one but starting at the top you can see how ludicrous these things are. If, as the rule states, you can never bet a horse to do something he hasn’t done before, then that means you can never bet a maiden race since none of those horses have ever won a race before.
“And you can never bet a horse stretching out or shortening up unless he’s done it before even though you have to understand that the best opportunity for a price probably is the first time that a horse tries a new distance. And I also suppose you can never bet a horse moving up in company, whether the move is from maidens to winners, allowance to stake or whatever because that horse has never beaten those types before. 
“Can’t you see how dumb and restricting a ‘rule’ such as that is? It may be a lot more time efficient to automatically eliminate horses based on some false criteria but it’s certainly not wise. Why would anyone want to limit their options?” 
The professional horse player said he wasn’t surprised that these types of unsubstantiated myths existed or even flourished because, when it comes to dispensing accurate information, thoroughbred racing has lagged way behind the advances made by other industries. 
“Most other businesses have become more sophisticated,” contended the player. “There are watchdog groups and consumer advocates checking the accuracy of their claims. When a dubious statement or suspect claim is made, it’s challenged and thoroughly investigated. 
“But in this game, people can still manufacture ‘rules’ and make up whatever they want. There are enough followers out there who will listen to people who know nothing and repeat what they say. After a while, the junk becomes accepted as fact.” 
Still, the player pointed out that such arbitrary “rules” can become an asset for astute bettors. 
“It’s terrific that this malarkey is in print because, not only are these statements preposterous, but the beauty is that people actually believe these myths. It’s total misinformation that the public seems to eat up. Every situation is different, of course, but bettors should be especially alert in these circumstances because they can sometimes capitalize on a widely held myth, go against the so-called ‘rule,’ and really get the best of it. If one horse is over bet because he meets some arbitrary criteria, then other horses must be under bet. Because of that, these myths can provide the competent player with tremendous value.” 
The player added that all “rules” have numerous exceptions and that the game can be simplified so even the average player can understand it. 
“In the end, the core of the game comes down to four basic questions: Which horses are fast enough to win? What is the likely pace scenario? Which horses have a better chance of winning than their odds suggest? And what is the racetrack bias? 
“If you can answer those four questions you’ve got 95 percent of the game figured out. Never mind the ‘rules.’ They’re nonsense.” 
</p>
    </div>

      
    </main>
    
    <footer>
        <p>Created by students of the College of Informatics at Northern Kentucky University</p>
    </footer>
</body>
</html>