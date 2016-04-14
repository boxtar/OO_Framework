<div class="container">

    <h1 style="text-align: center; padding-top: 35px">404 Page Not Found</h1>

    <?php if(config()->get('app.env')=='dev'):?>

    <p style="text-align: center">

        <?php echo $data['message'] ?>

    </p>

    <?php endif; ?>

</div>