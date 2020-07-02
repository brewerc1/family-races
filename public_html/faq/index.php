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
            <li><a href="http://localhost/races/index.php">Races</a></li>
            <li><a href="http://localhost/HOF/index.php">HOF</a></li>
            <li><a href="http://localhost/faq/index.php">FAQ</a></li>
            <li><a href="http://localhost/profile/index.php">Me</a></li>
            <li><a href="http://localhost/admin/index.php">Admin</a></li>
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
    </div>

      
    </main>
    
    <footer>
        <p>Created by students of the College of Informatics at Northern Kentucky University</p>
    </footer>
</body>
</html>