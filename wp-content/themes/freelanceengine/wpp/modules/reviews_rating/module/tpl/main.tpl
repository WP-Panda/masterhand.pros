{include '_head.tpl'}
	<a class="btn-refresh btn btn-outline-info btn-sm m-2" href="{$MODULE_URL}">
		<i class="fas fa-sync-alt">{$lang.refresh}</i></a>
	{*<a class="btn-refresh btn btn-outline-warning btn-sm m-2" href="{$MODULE_URL}&action=onCreateReview">*}
		{*<i class="fas fa-plus">{$lang.addReview}</i></a>*}

	<ul class="nav nav-tabs">
		<li class="nav-item">
			<a class="nav-link active" data-toggle="tab" href="#ratings">{$lang.ratings}</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#reviews">{$lang.reviews}</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#config">{$lang.settings}</a>
		</li>
	</ul>
	<div class="tab-content" id="tabs">
		<div class="tab-pane active show" id="ratings">
			<div class="input-group mb-3">
				<input type="text" class="form-control" id="search_field" placeholder="{$lang.search}...">
				<div class="input-group-append">
					<button class="btn btn-info run-search">
						<i class="fas fa-search"></i>
					</button>
					<button class="btn btn-secondary reset-search">
						{$lang.reset}
					</button>
				</div>
			</div>
			<table class="table table-hover">
				<thead class="font-weight-bold">
				<tr data-tb="ratings">
					<td class="col-sort" onclick="mod.setSort(this, 'user_id')"><i class="fas">{$lang.userId}</i></td>
					<td class="col-sort" onclick="mod.setSort(this, 'post_title')"><i class="fas"> {$lang.profileTitle}</i></td>
					{*<td class="col-sort" onclick="mod.setSort(this, 'total')"><i class="fas"> {$lang.total_votes}</i></td>*}
					<td class="col-sort" onclick="mod.setSort(this, 'votes')"><i class="fas"> {$lang.votes}</i></td>
					<td class="col-sort" onclick="mod.setSort(this, 'rating')"><i class="fas"> {$lang.rating}</i></td>
					<td class="col-sort" onclick="mod.setSort(this, 'countReviews')"><i class="fas">{$lang.countReviews}</i></td>
					<td class=""></td>
					<td class=""></td>
				</tr>
				</thead>
				<tbody>
				{include 'list_ratings.tpl'}
				</tbody>
				<tfoot>
				<tr>
					<td class="pagination-rating" colspan="6">
						{$rtPagination}
					</td>
				</tr>
				</tfoot>
			</table>
		</div>
		<div class="tab-pane" id="reviews">
			<div class="input-group mb-3">
				<input type="text" class="form-control" id="search_fieldRw" placeholder="{$lang.searchRw}...">
				<div class="input-group-append">
					<button class="btn btn-info run-search-rw">
						<i class="fas fa-search"></i>
					</button>
					<button class="btn btn-secondary reset-search-rw">
						{$lang.reset}
					</button>
				</div>
			</div>
			<table class="table table-hover">
				<thead class="font-weight-bold">
				<tr data-tb="reviews">
					<td class="col-sort" onclick="mod.setSort(this, 'created')"><i class="fas">{$lang.created}</i></td>
					<td class="">{$lang.username}</td>
					{*<td *}{*class="col-sort" onclick="mod.setSort(this, 'title')"*}{*><i class="fas"> {$lang.title}</i></td>*}
					<td {*class="col-sort" onclick="mod.setSort(this, 'vote')"*}><i class="fas"> {$lang.vote}</i></td>
					<td class="col-comment">{$lang.comment}</td>
					<td {*class="col-sort" onclick="mod.setSort(this, 'status')"*}><i class="fas"> {$lang.status}</i></td>
					<td {*class="col-sort" onclick="mod.setSort(this, 'pagetitle')"*}><i class="fas"> {$lang.reviewForProject}</i></td>
					<td class="">{*{$lang.countAnswers}*}</td>
					<td class=""></td>
				</tr>
				</thead>
				<tbody>
				{include 'list_reviews.tpl'}
				</tbody>
				<tfoot>
				<tr>
					<td class="pagination-reviews" colspan="9">
						{$rwPagination}
					</td>
				</tr>
				</tfoot>
			</table>
		</div>
		<div class="tab-pane" id="config">
			{include 'config.tpl'}
		</div>
	</div>
{include '_footer.tpl'}