<?php
// seed_products.php
include 'db.php';

$products = [
    [
        "name" => "Expresso",
        "category" => "boissons-chaudes",
        "description" => "Un café court et intense, parfait pour un coup de boost.",
        "price" => 2.50,
        "image" => "https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
    ],
    [
        "name" => "Cappuccino",
        "category" => "boissons-chaudes",
        "description" => "Café expresso avec du lait mousseux, saupoudré de cacao.",
        "price" => 3.80,
        "image" => "https://images.unsplash.com/photo-1570197788417-0e82375c9371?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
    ],
    [
        "name" => "Thé Vert Matcha",
        "category" => "boissons-chaudes",
        "description" => "Thé vert japonais moulu, riche en antioxydants.",
        "price" => 4.20,
        "image" => "https://images.unsplash.com/photo-1559056199-641a0ac8b55e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
    ],
    [
        "name" => "Chocolat Viennois",
        "category" => "boissons-chaudes",
        "description" => "Chocolat chaud généreux surmonté de crème fouettée.",
        "price" => 4.50,
        "image" => "https://images.unsplash.com/photo-1544787219-7f47ccb76574?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
    ],
    [
        "name" => "Smoothie Fruits Rouges",
        "category" => "boissons-froides",
        "description" => "Mélange de fraises, framboises et myrtilles fraîches.",
        "price" => 5.20,
        "image" => "https://images.unsplash.com/photo-1502741224143-90386d7f8c82?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
    ],
    [
        "name" => "Limonade Maison",
        "category" => "boissons-froides",
        "description" => "Limonade fraîchement pressée avec des citrons bio.",
        "price" => 3.90,
        "image" => "https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
    ]
];

echo "Seeding products...\n";

foreach ($products as $p) {
    $name = mysqli_real_escape_string($conn, $p['name']);
    $cat = mysqli_real_escape_string($conn, $p['category']);
    $desc = mysqli_real_escape_string($conn, $p['description']);
    $price = $p['price'];
    $img = mysqli_real_escape_string($conn, $p['image']);

    $query = "INSERT INTO products (name, category, description, price, image) VALUES ('$name', '$cat', '$desc', $price, '$img')";
    if (mysqli_query($conn, $query)) {
        echo "Added: $name\n";
    } else {
        echo "Error adding $name: " . mysqli_error($conn) . "\n";
    }
}

echo "Seeding complete.\n";
?>

