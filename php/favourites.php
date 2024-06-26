<?php
session_start();

// check if user is logged in or not if not redirect
if (!isset($_SESSION['apikey'])) {
    header('Location: ../php/login.php');
    exit();
}

$currentPage = 'favourites';

// Function to get all favourites
function getFavourites($apikey) {
    $data = array(
        'type' => 'GetAllFavourites',
        'apikey' => $apikey,
    );

    $json_data = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'u23535246:Toponepercent120');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return 'Curl error: ' . curl_error($ch);
    } else {
        return json_decode($response, true);
    }
}


// Function to remove a favourite
function removeFavourite($apikey, $filmId, $showId) {

    $add = "false";

    $data = array(
        "type" => "Favourite",
        "apikey" => $apikey,
        "add" => $add,
    );

    if(isset($showId)) {
        $data["show_id"] = $showId;
    } else {
        $data["film_id"] = $filmId;
    }


    $json_data = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'u23535246:Toponepercent120');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Handle form submission for removing a favourite
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove'])) {

    $apikey = $_SESSION['apikey'];
    $showId = isset($_POST['show_id']) ?  $_POST['show_id']:null;
    $filmId =isset($_POST['film_id']) ?  $_POST['film_id']:null;; 

    echo '<script>console.log("filmId: ' . $filmId . '");</script>';

    // Remove the favorite
    $removeResponse = removeFavourite($apikey, $filmId, $showId);
    // Refresh favourites list after removal
    $favourites = getFavourites($apikey);

} else {
    // Get favourites on page load
    $favourites = getFavourites($_SESSION['apikey']);
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/favourites.css" id="light-mode">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- <link rel="stylesheet" href="/css/homePage-dark.css" id="dark-mode"> -->
    <link rel="icon" href="../img/4.png" type="image/x-icon">
    <!-- the icons used in the website -->
    <link rel="stylesheet" href="../font-awesome-4.7.0/css/font-awesome.min.css">
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
        </nav>
</header>


<div class="container">
<?php if (isset($favourites['status']) && $favourites['status'] !== 'success') : ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($favourites['status']); ?></div>
        <?php elseif (!empty($favourites['data'])) : ?>
            <?php foreach ($favourites['data'] as $data) : ?>
                <div class="favBox">
                    <div class="fav-info">
                        <h3 class="title"><?php echo htmlspecialchars($data['Title']); ?></h3>
                        <img class="fav-image" src="<?php echo htmlspecialchars($data['PosterURL'])?>" alt="">
                        <p class="description"><?php echo htmlspecialchars($data['Description']); ?></p>
                        <p class="country"><?php echo htmlspecialchars($data['Country']); ?></p>
                        <p class="release-year"><?php echo htmlspecialchars($data['Release_Year']); ?></p>

                        <form method="POST" action="">
  
                        <?php 
                        if($data['type'] === 'film')
                        {
                            echo '<input type="hidden" name="film_id" value="'. $data['id'] . '">'; 
                        }
                        else
                        {
                            echo '<input type="hidden" name="show_id" value="'. $data['id'] . '">'; 
                        }

                        ?>

                        <!-- <button type="submit" name="remove" class="btn btn-danger">Remove</button> -->
                        <button  class="remove" type="submit">Remove</button>
                   </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No favorites found.</p>
        <?php endif; ?>
</div>
</body>
</html>
           