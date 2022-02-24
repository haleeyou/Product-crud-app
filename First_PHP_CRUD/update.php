<?php


//connect to the db we use pdo or mysqli------pdo support multiple db

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=product_crud', 'root', '');
//if connection is not succesful
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;

if (!$id) {
header('Location: index.php');
exit;
}


$statement = $pdo->prepare('SELECT * FROM products WHERE id = :id');
$statement->bindValue(':id',$id);
$statement->execute();
$product = $statement->fetch(PDO::FETCH_ASSOC);


$error = [];


$title = $product['title'];
$price = $product['price'];
$description = $product['description'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$title = $_POST['title'];
$description = $_POST['description'];
$price = $_POST['price'];




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
    $imagePath = $product['image'];


    if ($image && $image['tmp_name']){

        //remove existing product image
    if ($product['image']){
        unlink($product['image']);
    }

      //to make the path of an uploaded file unique by generating a random name folder all saved in the image folder
        $imagePath = 'images/'.randomString(8).'/'.$image['name'];

        mkdir(dirname($imagePath));
        
        move_uploaded_file($image['tmp_name'], $imagePath);
        
    }
   

$statement = $pdo->prepare("UPDATE products SET title = :title, image = :image,
                     description = :description, price = :price
                     WHERE id = :id");
                
$statement->bindValue(':title', $title);
$statement->bindValue(':image', $imagePath);
$statement->bindValue(':description', $description);
$statement->bindValue(':price', $price);
$statement->bindValue(':id', $id);
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
<style>
    .update-image {
        width: 150px;
    }
</style>
</head>
  <body>
    <p>
        <a href="index.php" class="btn btn-secondary"> Go back to Products </a>
    </p>

    <h1>Update Product: <b><?php echo $product['title'] ?> </b> </h1>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <div> <?php echo $error ?> </div>

        <?php endforeach; ?>

    </div>

    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <?php if($product['image']): ?>
            <img src="<?php echo $product['image'] ?>" class="update-image"/>
        <?php endif; ?>    

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