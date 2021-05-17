<?php 
/** Fenom template 'main.tpl' compiled at 2020-08-14 05:32:39 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><?php
/* main.tpl:1: {include '_head.tpl'} */
 $tpl->getStorage()->getTemplate('_head.tpl')->display($var); ?>
	<a class="btn-refresh btn btn-outline-info btn-sm m-2" href="<?php
/* main.tpl:2: {$MODULE_URL} */
 echo $var["MODULE_URL"]; ?>">
		<i class="fas fa-sync-alt"><?php
/* main.tpl:3: {$lang.refresh} */
 echo $var["lang"]["refresh"]; ?></i></a>
	
		

	<ul class="nav nav-tabs">
		<li class="nav-item">
			<a class="nav-link active" data-toggle="tab" href="#ratings"><?php
/* main.tpl:9: {$lang.ratings} */
 echo $var["lang"]["ratings"]; ?></a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#reviews"><?php
/* main.tpl:12: {$lang.reviews} */
 echo $var["lang"]["reviews"]; ?></a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#config"><?php
/* main.tpl:15: {$lang.settings} */
 echo $var["lang"]["settings"]; ?></a>
		</li>
	</ul>
	<div class="tab-content" id="tabs">
		<div class="tab-pane active show" id="ratings">
			<div class="input-group mb-3">
				<input type="text" class="form-control" id="search_field" placeholder="<?php
/* main.tpl:21: {$lang.search} */
 echo $var["lang"]["search"]; ?>...">
				<div class="input-group-append">
					<button class="btn btn-info run-search">
						<i class="fas fa-search"></i>
					</button>
					<button class="btn btn-secondary reset-search">
						<?php
/* main.tpl:27: {$lang.reset} */
 echo $var["lang"]["reset"]; ?>
					</button>
				</div>
			</div>
			<table class="table table-hover">
				<thead class="font-weight-bold">
				<tr data-tb="ratings">
					<td class="col-sort" onclick="mod.setSort(this, 'user_id')"><i class="fas"><?php
/* main.tpl:34: {$lang.userId} */
 echo $var["lang"]["userId"]; ?></i></td>
					<td class="col-sort" onclick="mod.setSort(this, 'post_title')"><i class="fas"> <?php
/* main.tpl:35: {$lang.profileTitle} */
 echo $var["lang"]["profileTitle"]; ?></i></td>
					
					<td class="col-sort" onclick="mod.setSort(this, 'votes')"><i class="fas"> <?php
/* main.tpl:37: {$lang.votes} */
 echo $var["lang"]["votes"]; ?></i></td>
					<td class="col-sort" onclick="mod.setSort(this, 'rating')"><i class="fas"> <?php
/* main.tpl:38: {$lang.rating} */
 echo $var["lang"]["rating"]; ?></i></td>
					<td class="col-sort" onclick="mod.setSort(this, 'countReviews')"><i class="fas"><?php
/* main.tpl:39: {$lang.countReviews} */
 echo $var["lang"]["countReviews"]; ?></i></td>
					<td class=""></td>
					<td class=""></td>
				</tr>
				</thead>
				<tbody>
				<?php
/* main.tpl:45: {include 'list_ratings.tpl'} */
 $tpl->getStorage()->getTemplate('list_ratings.tpl')->display($var); ?>
				</tbody>
				<tfoot>
				<tr>
					<td class="pagination-rating" colspan="6">
						<?php
/* main.tpl:50: {$rtPagination} */
 echo $var["rtPagination"]; ?>
					</td>
				</tr>
				</tfoot>
			</table>
		</div>
		<div class="tab-pane" id="reviews">
			<div class="input-group mb-3">
				<input type="text" class="form-control" id="search_fieldRw" placeholder="<?php
/* main.tpl:58: {$lang.searchRw} */
 echo $var["lang"]["searchRw"]; ?>...">
				<div class="input-group-append">
					<button class="btn btn-info run-search-rw">
						<i class="fas fa-search"></i>
					</button>
					<button class="btn btn-secondary reset-search-rw">
						<?php
/* main.tpl:64: {$lang.reset} */
 echo $var["lang"]["reset"]; ?>
					</button>
				</div>
			</div>
			<table class="table table-hover">
				<thead class="font-weight-bold">
				<tr data-tb="reviews">
					<td class="col-sort" onclick="mod.setSort(this, 'created')"><i class="fas"><?php
/* main.tpl:71: {$lang.created} */
 echo $var["lang"]["created"]; ?></i></td>
					<td class=""><?php
/* main.tpl:72: {$lang.username} */
 echo $var["lang"]["username"]; ?></td>
					
					<td ><i class="fas"> <?php
/* main.tpl:74: {$lang.vote} */
 echo $var["lang"]["vote"]; ?></i></td>
					<td class="col-comment"><?php
/* main.tpl:75: {$lang.comment} */
 echo $var["lang"]["comment"]; ?></td>
					<td ><i class="fas"> <?php
/* main.tpl:76: {$lang.status} */
 echo $var["lang"]["status"]; ?></i></td>
					<td ><i class="fas"> <?php
/* main.tpl:77: {$lang.reviewForProject} */
 echo $var["lang"]["reviewForProject"]; ?></i></td>
					<td class=""></td>
					<td class=""></td>
				</tr>
				</thead>
				<tbody>
				<?php
/* main.tpl:83: {include 'list_reviews.tpl'} */
 $tpl->getStorage()->getTemplate('list_reviews.tpl')->display($var); ?>
				</tbody>
				<tfoot>
				<tr>
					<td class="pagination-reviews" colspan="9">
						<?php
/* main.tpl:88: {$rwPagination} */
 echo $var["rwPagination"]; ?>
					</td>
				</tr>
				</tfoot>
			</table>
		</div>
		<div class="tab-pane" id="config">
			<?php
/* main.tpl:95: {include 'config.tpl'} */
 $tpl->getStorage()->getTemplate('config.tpl')->display($var); ?>
		</div>
	</div>
<?php
/* main.tpl:98: {include '_footer.tpl'} */
 $tpl->getStorage()->getTemplate('_footer.tpl')->display($var); ?><?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'main.tpl',
	'base_name' => 'main.tpl',
	'time' => 1588253053,
	'depends' => array (
  0 => 
  array (
    'main.tpl' => 1588253053,
  ),
),
	'macros' => array(),

        ));
