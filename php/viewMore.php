<?php
session_start(); // Start session to store user login status

// Function to make API request
function makeApiRequest($data)
{
    // Create a new cURL resource
    $ch = curl_init();
 
    // Set the URL
    curl_setopt($ch, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');

    // Set the request method to POST
    curl_setopt($ch, CURLOPT_POST, 1);

    // Set the request data as JSON
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Set the Content-Type header
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Set basic authentication credentials
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'u23535246:Toponepercent120'); // Replace with your actual credentials

    // Return response instead of outputting it
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);

    // Close cURL resource
    curl_close($ch);

    // Decode the JSON response
    return json_decode($response, true);
}

// Function to handle adding to favorites
function addToFavorites($apiKey, $filmId, $showId)
{
    // Check if API key is available
    if (!$apiKey) {
        // Redirect to login page if API key is not available
        header("Location:../php/login.php");
        exit();
    }

    // Check if the add button is clicked
    $add = isset($_POST['addToFavorites']) ? "true" : "false";


    if (isset($_GET['name']))       //show
    {
        $data = array(
            "type" => "Favourite",
            "apikey" => $apiKey,
            "add" => $add,
            "show_id" => $showId
        );
    } else {
        $data = array(                      //movie
            "type" => "Favourite",
            "apikey" => $apiKey,
            "add" => $add,
            "film_id" => $filmId
        );
        echo '<script>alert("i am a film: ' . $data['film_id'] . '");</script>';
    }
    // Prepare data for adding to favorites


    // Make API request
    $responseData = makeApiRequest($data);
    var_dump($responseData); // Add this line

    // Check if the request was successful
    if ($responseData['status'] === 'success') {
        // Redirect to favourites.php after successfully adding to favorites
        header("Location: ../php/favourites.php");
        exit();
    } else {
        // Failed to add to favorites
        //  echo '<script>alert("Failed to add to My List: ' . $responseData['error'] . '");</script>';
    }
}

// Check if the user is not logged in, redirect to login page
$apiKey = isset($_SESSION['apikey']) ? $_SESSION['apikey'] : null;

// Prepare data for JSON request
if (isset($_GET['name'])) {
    $name = urldecode($_GET['name']);
    $data = array(
        "type" => "GetAllSeries",
        "limit" => 1,
        "search" => array(
            "Name" => $name
        ),
        "return" => "all"
    );
} else {
    $title = urldecode($_GET['title']);
    $data = array(
        "type" => "GetAllMovies",
        "limit" => 1,
        "search" => array(
            "Title" => $title
        ),
        "return" => "all"
    );
}

// Make API request
$responseData = makeApiRequest($data);

// Check if the request was successful
if ($responseData['status'] === 'success') {
    // Process the data
    $movies = $responseData['data'][0];
} else {
    // Handle error response
    $error = $responseData['data'];
}

// Handle adding to favorites if form is submitted
if (isset($_POST['addToFavorites'])) {


    if (isset($_GET['name'])) {
        addToFavorites($apiKey, null, $movies["ID"]);
    } else {
        addToFavorites($apiKey, $movies["ID"], null);
    }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/viewMore.css" id="light-mode">
    <link rel="icon" href="../img/4.png" type="image/x-icon">

    <!-- <link rel="stylesheet" href="file:///E:fontawesome/css/all.css"> -->
    <title>CineTech</title>
</head>


<body>
    <!--Header-->
    <header>
        <!-- convert this image to a webm so it actually plays  -->
        <nav>
            <div class="logo_ul">
                <img src="../img/4.png" alt="">
                <ul>
                    <li>
                        <a href="../php/homePage.php">Home</a>
                    </li>
                    <li>
                        <a href="../php/movies.php">Movies</a>
                    </li>
                    <li>
                        <a href="../php/series.php">Series</a>
                    </li>
                    <li>
                        <a href="../php/recAdded.php">Recently Added</a>
                    </li>
                    <li>
                        <a href="../php/favourites.php">My List</a>
                    </li>
                </ul>
            </div>
            <!-- <div class="search_user">
                <input type="text" placeholder="Search..." id="search_input">
                <img src="../img/UserPFP.jpeg" alt="">
            </div> -->
        </nav>

        <div class="ViewDetails">
            <div class="content">
                <div class="content-image">
                    <img class="movieImg" src="<?php echo $movies['PosterURL'] ?>" alt="">
                </div>
            </div>

            <div class="content-details">
                <!-- Create a div for the description as well as the other-->
                <div class="description">
                    <h2>Description: </h2>
                    <h3><?php echo $movies['Description'] ?></h3><br>
                </div>

                <div class="Genres">
                    <h3>Genre: <?php echo $movies['Genre'] ?> </h3><br>
                </div>

                <div class="yearRelease">
                    <h3>Year Of Release: <?php echo $movies['Release_Year'] ?></h3><br>
                </div>

                <div class="movieRating">
                    <h3>CineTech Rating: <?php echo $movies['CineTech_Rating'] ?></h3><br>
                </div>

                <div class="userRating">
                    <!-- <h3>Ratings: </h3> -->
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET); ?>">
                    <div class="star-icon">
                        <input type="radio" name="rating" id="star5" value="5">
                        <label for="star5"></label>
                        <input type="radio" name="rating" id="star4" value="4">
                        <label for="star4"></label>
                        <input type="radio" name="rating" id="star3" value="3">
                        <label for="star3"></label>
                        <input type="radio" name="rating" id="star2" value="2">
                        <label for="star2"></label>
                        <input type="radio" name="rating" id="star1" value="1">
                        <label for="star1"></label>
                    </div>
                    <div class="score"></div>
                    <button type="submit" class="btn"  name="submitRating">Submit Rating</button>
                </form>
                </div>

                <?php
            // Check if the form is submitted
            if (isset($_POST['submitRating']) && isset($_POST['rating'])) {
                // Get the selected rating
                $rating = $_POST['rating'];

                // Add logic here to get $filmId and $showId depending on your implementation
                if (isset($_GET['name'])) {
                    // For series, you might get the show ID from the URL parameter
                    $showId = isset($_GET['id']) ? $_GET['id'] : null;
                    $filmId = null;
                } else {
                    // For movies, you might get the film ID from the API response or another source
                    $showId = null;
                    $filmId = isset($movies['ID']) ? $movies['ID'] : null;
                }

                // Call the function to add the rating
                if (isset($_GET['name'])) {
                    addToRating($apiKey, null, $movies["ID"], $rating);
                } else {
                    addToRating($apiKey, $movies["ID"], null, $rating);
                }
            }

            // Function to handle adding the rating
            function addToRating($apiKey, $filmId, $showId, $rating)
            {
               
                // Prepare data for adding rating
                if (isset($_GET['name'])) {
                    $data = array(
                        "type" => "AddRating",
                        "apikey" => $apiKey,
                        "item" => "series",
                        "ID" => $showId,
                        "rating" => $rating
                    );
                    
                } else {
                    $data = array(
                        "type" => "AddRating",
                        "apikey" => $apiKey,
                        "item" => "movie",
                        "ID" => $filmId,
                        "rating" => $rating
                    );
                }

                // Make API request
                $responseData = makeApiRequest($data);

                // Check if the request was successful
                if ($responseData['status'] === 'success') {
                    
                } else {
                    // Handle error response
                    echo '<script>alert("Failed to add rating: ' . $responseData['error'] . '");</script>';
                }
            }

        ?>




                <script src="viewMore.js"></script>

                <div class="actors">
                    <h3> </h3><br>
                </div>


                <button class="trailer">
                    <a href=" <?php echo ' ' ?> ">Trailer</a><br>
                </button>
                <?php
                if(isset($_POST['search_user'])) {
                    $userN = $_POST['search_user'];

                    if (isset($_GET['name'])) {
                        $shareData = array(
                            "type" => "ShareSeries",
                            "apikey" => $apiKey,
                            "username" => $userN,
                            "id" => $movies['ID']
                        );
                    } else {
                        $shareData = array(
                            "type" => "ShareFilm",
                            "apikey" => $apiKey,
                            "username" => $userN,
                            "id" => $movies['ID']
                        );
                    }

                    $shareReq = makeApiRequest($shareData);
                    var_dump($shareReq['data']); // Add this line
                
                    // Check if the request was successful
                    if ($shareReq['status'] === 'success') {
                        echo '<script>alert("Movie shared with: "+'.$userN .') </script>';
                    } else {
                        echo 'Error: ' . $shareReq['message'];
                    }
                }
                    
    
                
                ?>
                <form method="post" id="shareForm" action="<?php echo $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET); ?>">
                    <input name="search_user" type="hidden" id="shareUserHidden">
                    <button type="button" class="btn" id="shareButton">Share</button>
                </form>

                <div id="shareModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <!-- Add your modal content here -->
                        <h2>Share to User</h2>

                        <input name="search_user" type="text" id="shareUser" placeholder="Enter username">
                        <button id="shareSubmit">Send</button>
                    </div>
                </div>


                <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET); ?>">
                    <button type="submit" class="watchList" name="addToFavorites">Add to MyList</button>
                </form>

            </div>
        </div>

        <?php
        //header("Access-Control-Allow-Origin: http://localhost");
        //header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        //header("Access-Control-Allow-Headers: Content-Type, Authorization");


        $mov = array();
        // Check if the login form is submitted
        // Prepare the data for JSON request

        if (isset($_GET['name'])) {

            $data = array(
                "type" => "GetAllSeries",
                'limit' => 20,
                "search" => array(
                    "genre" => $movies['Genre']
                ),
                'return' => "all"
            );
        } else {
            //$title = urldecode($_GET['title']);
            $data = array(
                "type" => "GetAllMovies",
                'limit' => 20,
                "search" => array(
                    "genre" => $movies['Genre']
                ),
                'return' => "all"
            );
        }

        // Convert data to JSON format
        $json_data = json_encode($data);

        // Create a new cURL resource
        $ch = curl_init();

        // Set the URL
        curl_setopt($ch, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');

        // Set the request method to POST
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the request data as JSON
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        // Set the Content-Type header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        // Set basic authentication credentials
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'u23535246:Toponepercent120'); // Replace with your actual credentials

        // Return response instead of outputting it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($ch);

        // Close cURL resource
        curl_close($ch);

        // Decode the JSON response
        $responseData = json_decode($response, true);

        // Check if the request was successful

        if ($responseData['status'] === 'success') {
            // Process the listings data and display on the page
            $mov = $responseData['data'];
        } else {
            // Handle error response
            $error = $responseData['data'];
        }
        ?>


        <section>
            <h4>People Also Like</h4>
            <div class="cards">
                <?php

                if (isset($_GET['name'])) {

                    if (isset($mov)) {
                        foreach ($mov as $idx) {

                            //$nameSeries = urlencode();
                            echo '<a href="viewMore.php?name=' . $idx['Name'] . '" class="card">';
                            echo '<img src= " ' . $idx['PosterURL'] . '" alt="" class="poster">';
                            echo '<div class="rest_card">';
                            echo '<img src= " ' . $idx['PosterURL'] . '" alt="" >';
                            echo '<div class="cont">';
                            echo '<h4>' . $idx['Name'] . '</h4>';
                            echo '<div class = "sub">';
                            echo '<p>' . $idx['Genre'] . ' , ' . $idx['Release_Year'] . '</p>';
                            echo '<h3><span>CineTech</span>';
                            echo '<i class="fa fa-star" aria-hidden="true"></i>' . $idx['IMDB_score'] . '</h3>';  // assuming 'rating' key exists in the array
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</a>';
                        }
                    }
                } else {
                    if (isset($mov)) {
                        foreach ($mov as $index) {
                            //$movtitle = urlencode();
                            echo '<a href="viewMore.php?title=' . $index['Title'] . '" class="card">';
                            echo '<img src= " ' . $index['PosterURL'] . '" alt="" class="poster">';
                            echo '<div class="rest_card">';
                            echo '<img src= " ' . $index['PosterURL'] . '" alt="" >';
                            echo '<div class="cont">';
                            echo '<h4>' . $index['Title'] . '</h4>';
                            echo '<div class = "sub">';
                            echo '<p>' . $index['Genre'] . ' , ' . $index['Release_Year'] . '</p>';
                            echo '<h3><span>CineTech</span>';
                            echo '<i class="fa fa-star" aria-hidden="true"></i>' . $index['IMDB_score'] . '</h3>';  // assuming 'rating' key exists in the array
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</a>';
                        }
                    }
                }

                ?>
            </div>
        </section>
    </header>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Get the modal
            var modal = document.getElementById("shareModal");

            // Get the button that opens the modal
            var btn = document.getElementById("shareButton");

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // When the user clicks the button, open the modal
            btn.onclick = function(event) {
                event.preventDefault(); // Prevent form submission
                modal.style.display = "block";
            }

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            // Handle the share button inside the modal
            var shareSubmit = document.getElementById("shareSubmit");
            shareSubmit.onclick = function() {
                var shareUser = document.getElementById("shareUser").value;
                // alert("Share to User: " + shareUser);
                document.getElementById("shareUserHidden").value = shareUser;

                // Here you can add your logic to handle the share action
                document.getElementById("shareForm").submit();
                modal.style.display = "none";
            }
        });
    </script>
</body>

</html>