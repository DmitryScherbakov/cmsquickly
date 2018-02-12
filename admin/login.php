<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Login :: CrusAdmin</title>
		<meta name="robots" content="noindex">
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" type="text/css">
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css" type="text/css">
		<link rel="stylesheet" href="/admin/css/custom.css" type="text/css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" type="text/javascript"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="//cdn.tinymce.com/4/tinymce.min.js" type="text/javascript"></script>
		<script src="/admin/js/scripts.js" type="text/javascript"></script>
	</head>
	<body role="document">
		<!-- Fixed navbar -->
		<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" href="http://crusadmin.com" target="_blank">CrusAdmin</a>
				</div>
			</div>
		</div>

		<div class="container-fluid" role="main">
			<div class="row">
				<div id="mainbox">
					<div class="page-header">
						<h1>Login</h1>
					</div>
					<div class="row-data">
						<form class="form-horizontal" role="form" action="/admin/index.php" method="post">
							<div class="form-group">
								<label for="login" class="col-sm-2 control-label">Login</label>
								<div class="col-sm-10">
									<input class="form-control" id="login" name="login" value="" type="text">
								</div>
							</div>
							<div class="form-group">
								<label for="pass" class="col-sm-2 control-label">Password</label>
								<div class="col-sm-10">
									<input class="form-control" id="pass" name="pass" value="" type="password">
								</div>
							</div>
							<input name="id" value="" type="hidden">
							<div class="form-group">
								<div class="col-sm-offset-2 col-sm-10">
									<button type="submit" name="act" value="login" class="btn btn-primary">Log In</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>