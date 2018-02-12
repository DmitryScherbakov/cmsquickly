<?php include(TMPLS . 'header.php') ?>
<div class="page-header">
	<h1><?= $this->page_title ?></h1>
</div>
<div class="row">
	<a href="<?= ROOTURL ?>sitemap?act=sitemap" class="btn btn-primary" id="genSitemap"><?= _t('Generate sitemap.xml') ?></a>
</div>
<?php $this->startJs() ?>
<script>
	$(function () {
		$('#genSitemap').on('click', function (e) {
			e.preventDefault();
			var jqxhr = $.ajax({
				method: "GET",
				url: '<?= ROOTURL ?>settings',
				dataType: 'json',
				data: {act: "sitemap", 'ajax': 1}
			}).done(function (data) {
				if (data.error) {
					showAlert('error', '<?= _t("An error occured while generating sitemap!") ?>' + data.error);
				} else {
					showAlert('success', '<?= _t("Sitemap generated succesfully") ?>');
				}
			}).fail(function () {
				showAlert('error', '<?= _t("An error occured while generating sitemap!") ?>');
			});
		});
	});
</script>
<?php $this->stopJs() ?>
<?php include(TMPLS . 'footer.php') ?>