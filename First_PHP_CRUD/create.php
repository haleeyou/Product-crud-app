<?php


//connect to the db we use pdo or mysqli------pdo support multiple db

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=product_crud', 'root', '');
//if connection is not succesful
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$error = [];


$title = '';
$price = '';
$description = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$title = $_POST['title'];
$description = $_POST['description'];
$price = $_POST['price'];
$date = date('Y-m-d H:i:s');



//validation: check if a field is empty

if (!$title) {
$errors[] = 'Product title is required';
 
}

if (!$price) {
    $errors[] = 'Product price is required';
     
 }

 //Create an image folder if none exist
if (!is_dir('images')){
    mkdir('images');
} 

if (empty($errors)) {

    $image = $_FILES['image'] ?? null;
    $imagePath = '';
    if ($image && $image['tmp_name']){
      //to make the path of an uploaded file unique by generating a random name folder all saved in the image folder
        $imagePath = 'images/'.randomString(8).'/'.$image['name'];

        mkdir(dirname($imagePath));
        
        move_uploaded_file($image['tmp_name'], $imagePath);
        
    }
   

$statement = $pdo->prepare("INSERT INTO products (title, image, description, price, create_date)
                VALUES (:title, :image , :description, :price, :date)");

$statement->bindValue(':title', $title);
$statement->bindValue(':image', $imagePath);
$statement->bindValue(':description', $description);
$statement->bindValue(':price', $price);
$statement->bindValue(':date', $date);
$statement->execute();

//redirect user back to home page once they finish creating product

header('Location: index.php');

}

}


//function to generate random string

function randomString($n){
$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$str = '';
for ($i = 0; $i < $n; $i++) {
    $index = rand(0, strlen($characters)-1);
    $str .= $characters[$index];
}

return $str;

}

?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="app.css">
<title>Products Catalog</title>
  </head>
  <body>
    <h1>Create new Product</h1>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <div> <?php echo $error ?> </div>

        <?php endforeach; ?>

    </div>

    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
         <div class="form-group">
            <label >Product Image</label><br>
            <input type="file" name="image">
        </div>
        <br>
        <div class="form-group">
            <label >Product Title</label>
            <input type="text" name="title" class="form-control" value = <?php echo $title ?> >
        </div>
        <br>
        <div class="form-group">
            <label >Product Description</label>
            <textarea class="form-control" name="description" value = <?php echo $description ?>></textarea>
        </div>
        <br>
        <div class="form-group">
            <label >Product Price</label>
            <input type="number" step="0.1" name="price" class="form-control" value = <?php echo $price ?>>
        </div> 

        <br>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
    

  
  </body>
</html>  