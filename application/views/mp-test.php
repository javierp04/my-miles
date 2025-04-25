<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>MY MILES - Administra tus búsquedas Smiles</title>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <script>
        const mp = new MercadoPago('TEST-de745ce8-986f-4d0a-8f77-375f00322e17', {locale : 'es-AR'});
        const bricksBuilder = mp.bricks();
    </script>
</head>

<body>
    <div id="wallet_container" style="width: 400px;"></div>
    <script>
        mp.bricks().create("wallet", "wallet_container", {
                    initialization: {
                        preferenceId: "<?= $preference->id ?>",
                    },
                    customization: {
                        texts: {
                            valueProp: 'smart_option',
                        },
                    }
                });

                // mp.checkout({
                //    preference: {
                //         id: "<?= $preference->id ?>",
                //     },
                //     render : {
                //         container : '.wallet_container',
                //         label : 'Pagar con MP'
                //     }
                // });
    </script>
</body>

</html>