{include '_head.tpl'}
	<a class="btn-refresh btn btn-outline-info btn-sm m-2" href="{$MODULE_URL}">
		<i class="fas fa-sync-alt">{$lang.refresh}</i></a>
	{*<a class="btn-refresh btn btn-outline-warning btn-sm m-2" href="{$MODULE_URL}&action=onCreateReview">*}
		{*<i class="fas fa-plus">{$lang.addReview}</i></a>*}

	{*<ul class="nav nav-tabs">*}
		{*<li class="nav-item">*}
			{*<a class="nav-link active" data-toggle="tab" href="#post_like">{$lang.ratings}</a>*}
		{*</li>*}
		{*<li class="nav-item">*}
			{*<a class="nav-link" data-toggle="tab" href="#reviews">{$lang.reviews}</a>*}
		{*</li>*}
	{*</ul>*}
	<div class="tab-content" id="tabs">
		<div class="tab-pane active show" id="post_like">
			<div class="row">
				<div class="col-md-5">
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
				<table class="table table-hover source-table-items">
					<thead class="font-weight-bold">
					<tr>
						<td class="col-sort" onclick="mod.setSort(this, 'ID')"><i class="fas">{$lang.userId}</i></td>
						<td class="col-sort" onclick="mod.setSort(this, 'post_title')"><i class="fas"> {$lang.docTitle}</i></td>
						<td class="col-sort" onclick="mod.setSort(this, 'likes')"><i class="fas"> {$lang.countLikes}</i></td>
						<td class=""></td>
						<td class=""></td>
					</tr>
					</thead>
					<tbody id="listItems">
					{include 'list_post.tpl'}
					</tbody>
					<tfoot>
					<tr>
						<td class="source-pagination-items" colspan="5">
							{$postPagination}
						</td>
					</tr>
					</tfoot>
				</table>
				</div>
				<div class="col-md-7">
					<div class="row">
					<div class="col-xs-6 wrap-chartPie" style="width: 300px; height: 300px">
						<canvas id="chartPie" style="width: 100%;height: 100%"></canvas>
					</div>
					<div class="col-xs-6 wrap-chartLine" style="width: 400px; height: 300px">
						<canvas id="chartLine" style="width: 100%;height: 100%"></canvas>
					</div>
					</div>
				</div>
			</div>
		</div>

		{*<div class="tab-pane" id="reviews">*}
			{*<div class="input-group mb-3">*}
				{*<input type="text" class="form-control" id="search_fieldRw" placeholder="{$lang.searchRw}...">*}
				{*<div class="input-group-append">*}
					{*<button class="btn btn-info run-search-rw">*}
						{*<i class="fas fa-search"></i>*}
					{*</button>*}
					{*<button class="btn btn-secondary reset-search-rw">*}
						{*{$lang.reset}*}
					{*</button>*}
				{*</div>*}
			{*</div>*}
			{*<table class="table table-hover">*}
				{*<thead class="font-weight-bold">*}
				{*<tr data-tb="reviews">*}
					{*<td class="col-sort" onclick="mod.setSort(this, 'created')"><i class="fas">{$lang.created}</i></td>*}
					{*<td class="">{$lang.username}</td>*}
					{*<td *}{**}{*class="col-sort" onclick="mod.setSort(this, 'title')"*}{**}{*><i class="fas"> {$lang.title}</i></td>*}
					{*<td *}{*class="col-sort" onclick="mod.setSort(this, 'vote')"*}{*><i class="fas"> {$lang.vote}</i></td>*}
					{*<td class="col-comment">{$lang.comment}</td>*}
					{*<td *}{*class="col-sort" onclick="mod.setSort(this, 'status')"*}{*><i class="fas"> {$lang.status}</i></td>*}
					{*<td *}{*class="col-sort" onclick="mod.setSort(this, 'pagetitle')"*}{*><i class="fas"> {$lang.reviewForProject}</i></td>*}
					{*<td class="">*}{*{$lang.countAnswers}*}{*</td>*}
					{*<td class=""></td>*}
				{*</tr>*}
				{*</thead>*}
				{*<tbody>*}
				{*{include 'list_reviews.tpl'}*}
				{*</tbody>*}
				{*<tfoot>*}
				{*<tr>*}
					{*<td class="pagination-reviews" colspan="9">*}
						{*{$rwPagination}*}
					{*</td>*}
				{*</tr>*}
				{*</tfoot>*}
			{*</table>*}
		{*</div>*}
	</div>
{include '_footer.tpl'}