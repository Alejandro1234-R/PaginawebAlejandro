<?php
function asegurarProductosIniciales($conn)
{
    $productos = [
        ['Camiseta Oversize Negra', 60000, 'Camiseta oversize negra, comoda y versatil para uso diario.', 'imagenes/img/Camiseta Oversize Negra.jpeg'],
        ['SILENCE', 70000, 'Oversize en cuero con estilo urbano.', 'imagenes/img/Oversize en cuero.jpeg'],
        ['ESSENTIALS', 60000, 'Camiseta oversize Essentials.', 'img2/Oversize Essentials.jpeg'],
        ['Cloud De Ariana Grande', 330000, 'Perfume Cloud de Ariana Grande.', 'imagenes/Perfumes/Cloud De Ariana Grande.jpeg'],
        ['MONASTERY', 60000, 'Camiseta oversize Monastery.', 'img2/Oversize Monastery 1.jpeg'],
        ['VETEMENTS', 60000, 'Camiseta oversize sencilla blanca.', 'img2/Oversize sencilla blanca.jpeg'],
        ['Valentino Uomo Born In Roma', 300000, 'Perfume Valentino Uomo Born In Roma.', 'imagenes/Perfumes/Valentino Uomo Born In Roma 100ML EDT 1.1 Premium.jpeg'],
        ['Born x Raised', 80000, 'Gorra Born x Raised.', 'imagenes/Gorras/BornXRaised Gorra.jpeg'],
        ['Bisons', 80000, 'Gorra Bisons.', 'imagenes/Gorras/Bisons Gorra.jpeg'],
        ['Camiseta ACID DRAGON', 60000, 'Camiseta oversize ACID DRAGON.', 'imagenes/Camisetas/ACID DRAGON 1.1.png'],
        ['Camiseta GAME B', 60000, 'Camiseta oversize GAME B.', 'imagenes/Camisetas/GAME B.png'],
        ['Camiseta DUALITY BLK', 60000, 'Camiseta oversize DUALITY BLK.', 'imagenes/Camisetas/DUALITY BLK 2.png'],
        ['Gorra Anaheim Angels', 80000, 'Gorra Anaheim Angels.', 'imagenes/Gorras/Anaheim Angels Gorra.jpeg'],
        ['Gorra Diamond Backs', 80000, 'Gorra Diamond Backs.', 'imagenes/Gorras/Diamond Backs Gorra.jpeg'],
        ['Gorra Houston Astros', 80000, 'Gorra Houston Astros.', 'imagenes/Gorras/Houston Astros Gorra.jpeg'],
        ['Gorra Los Angeles Dodgers', 80000, 'Gorra Los Angeles Dodgers.', 'imagenes/Gorras/LosAngeles Dodgers Gorra.jpeg'],
        ['Gorra Piratas PITTSBURGH', 80000, 'Gorra Piratas PITTSBURGH.', 'imagenes/Gorras/Piratas PITTSBURGH Gorra.jpeg'],
        ['Gorra Raiders', 80000, 'Gorra Raiders.', 'imagenes/Gorras/Raiders Gorra.jpeg'],
        ['Adore De Christian Dior Mujer', 200000, 'Perfume Jadore de Christian Dior para mujer.', "imagenes/Perfumes/J'adore De Christian Dior 100 ML Mujer EDP.jpeg"],
        ['Khamrah De Lattafa Hombre', 280000, 'Perfume Khamrah de Lattafa para hombre.', 'imagenes/Perfumes/Khamrah De Lattafa 100 ML Hombre EDP.jpeg'],
        ['Lattafa Noble Blush EDP', 160000, 'Perfume Lattafa Noble Blush EDP.', 'imagenes/Perfumes/Lattafa Noble Blush EDP 100ml 1.1 Premium.jpeg'],
        ['Euphoria Calvin Klein Mujer', 180000, 'Perfume Euphoria Calvin Klein para mujer.', 'imagenes/Perfumes/Euphoria Calvin Klein 100 ML Mujer EDP.jpeg'],
        ['Eau de toilette Gucci Bloom 100 ML', 160000, 'Eau de toilette Gucci Bloom 100 ML.', 'imagenes/Perfumes/Eau de toilette Gucci Bloom 100 ML.jpeg'],
        ['Ck One Shock For Him De Calvin Klein', 180000, 'Perfume Ck One Shock For Him de Calvin Klein.', 'imagenes/Perfumes/Ck One Shock For Him De Calvin Klein 100 ML Hombre EDT.jpeg'],
        ['Ck One Shock For Her 100 ML Mujer', 180000, 'Perfume Ck One Shock For Her 100 ML mujer.', 'imagenes/Perfumes/Ck One Shock For Her 100 ML Mujer EDT.jpeg'],
        ['Ck In 2U De Calvin Klein Mujer EDT', 210000, 'Perfume Ck In 2U de Calvin Klein para mujer EDT.', 'imagenes/Perfumes/Ck In 2U De Calvin Klein 100 ML Mujer EDT.jpeg'],
        ['Burberry Her De Burberry Mujer EDP', 210000, 'Perfume Burberry Her de Burberry para mujer EDP.', 'imagenes/Perfumes/Burberry Her De Burberry 100 ML Mujer EDP.jpeg'],
        ['9 PM Afnan EDP Original', 250000, 'Perfume 9 PM Afnan EDP original.', 'imagenes/Perfumes/9 PM Afnan EDP 1.1 Premium.jpeg'],
        ['Oversize en cuero', 60000, 'Camiseta oversize en cuero.', 'imagenes/img/Oversize en Cuero.jpeg'],
        ['Oversize LV', 60000, 'Camiseta oversize LV.', 'img2/Oversize LV 2.jpeg'],
        ['Oversize Monastery 2', 60000, 'Camiseta oversize Monastery.', 'img2/Oversize Monastery 2.jpeg'],
        ['Oversize NIKE', 60000, 'Camiseta oversize NIKE.', 'img2/Oversize Nike.jpeg'],
    ];

    $buscar = $conn->prepare("SELECT id FROM productos WHERE imagen = ? LIMIT 1");
    $insertar = $conn->prepare("INSERT INTO productos (nombre, precio, descripcion, imagen) VALUES (?, ?, ?, ?)");

    foreach ($productos as $producto) {
        [$nombre, $precio, $descripcion, $imagen] = $producto;
        $buscar->bind_param("s", $imagen);
        $buscar->execute();
        $buscar->store_result();

        if ($buscar->num_rows === 0) {
            $insertar->bind_param("sdss", $nombre, $precio, $descripcion, $imagen);
            $insertar->execute();
        }
    }

    $buscar->close();
    $insertar->close();
}
?>
