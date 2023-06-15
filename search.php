<!DOCTYPE html>
<html lang="el">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Asset Search</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <style>
            html,
            body,
            .login-box {
            height: 100%;
            }
        </style>
</head>

<body>

    <div class="valign-wrapper row login-box">
    <div class="col card hoverable s10 pull-s1 m6 pull-m3 l4 pull-l4">
        <form method="get" action="results.php">
        <div class="card-content">
            <span class="card-title">Αναζήτηση εξοπλισμού</span>
            <div class="row" style="margin-top: 40px;">
            <div class="input-field col s12">
                <label for="beacon">Όνομα ή περιγραφή εξοπλισμού</label>
                <input type="text" name="beacon" id="beacon" />
            </div>
            </div>
        </div>
        <div class="card-action right-align">
            <button class="waves-effect waves-teal btn-flat" type="reset" name="reset">Επαναφορά</button>
            <button class="btn waves-effect waves-light" type="submit" name="submit">Αναζήτηση</button>
        </div>
        </form>
    </div>
    </div>
</body>
</html>