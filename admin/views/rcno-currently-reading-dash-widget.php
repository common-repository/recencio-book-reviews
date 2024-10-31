<?php {

	$progress         = get_option( $this->widget_id, array() );
	$most_recent      = end( $progress );
	$book_cover       = isset( $most_recent['book_cover'] ) ? $most_recent['book_cover'] : '';
	$book_title       = isset( $most_recent['book_title'] ) ? $most_recent['book_title'] : '';
	$book_author      = isset( $most_recent['book_author'] ) ? $most_recent['book_author'] : '';
	$current_page     = isset( $most_recent['current_page'] ) ? $most_recent['current_page'] : 0;
	$num_of_pages     = isset( $most_recent['num_of_pages'] ) ? $most_recent['num_of_pages'] : 1;
	$progress_comment = isset( $most_recent['progress_comment'] ) ? $most_recent['progress_comment'] : '';
	$finished_book    = isset( $most_recent['finished_book'] ) ? $most_recent['finished_book'] : 0;
	$last_updated     = isset( $most_recent['last_updated'] ) ? $most_recent['last_updated'] : '';
	$percentage       = ! empty( $num_of_pages ) ? round( ( $current_page / $num_of_pages ) * 100 ) : 0;

?>
<div id="admin-currently-reading">
	<admin-currently-reading>
		<div class="rcno-currently-loading">
			<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
		</div>
	</admin-currently-reading>
</div>

<template id="admin-reading-template">
	<form @submit.prevent="submitData" id="<?php echo $this->widget_id . '_form'; ?>">
		<transition name="fade">
			<div v-if="message" id="feedback">
				<p id="message" class="updated notice notice-success">{{ message }}</p>
			</div>
		</transition>
		<div class="currently-reading-wrapper" :data-source="data_source">
			<div class="book">
				<div v-if="curr_update.book_cover">
					<img :src="curr_update.book_cover" alt="currently-reading-book-cover" style="width: 100px"
						 id="rcno_currently_reading_cover">
					<div class="progress-bar">
						 <span class="percentage-value">{{ percentage + '%' }}</span>
						<div class="percentage" :style="{ width: percentage + '%' }"></div>
					</div>
				</div>
				<div v-else>
					<input type="hidden" id="rcno_currently_reading_upload_field">
					<div @click="uploadCover" class="rcno_currently_upload_button book-upload-container">
						<p class="dashicons dashicons-welcome-add-page"></p>
						<p><?php _e( 'Book Cover', 'recencio-book-reviews' ); ?></p>
					</div>
				</div>
			</div>
			<div class="info">
				<div class="form-field input-text-wrap">
					<input v-model="curr_update.book_title" :disabled="disabled" type="text" id="rcno_currently_reading_book_title"
						   placeholder="<?php _e( 'Book Title', 'recencio-book-reviews' ) ?>" required />
					<input v-model="curr_update.book_author" :disabled="disabled" type="text" id="rcno_currently_reading_book_author"
						   placeholder="<?php _e( 'Book Author', 'recencio-book-reviews' ) ?>" required />
					<p class="rcno_current_page">
						<strong><?php _e( 'Currently on page ', 'recencio-book-reviews' ) ?>
							<input v-model="curr_update.current_page" type="number" id="rcno_current_page_number" min="1" > /
							<input v-model="curr_update.num_of_pages" type="number" :disabled="disabled" id="rcno_current_num_pages" min="1" />
						</strong>
					</p>
					<textarea v-model="curr_update.progress_comment" name="" id="rcno_currently_reading_book_comment" cols="30" rows="4" placeholder="<?php
					_e( 'Comment on progress', 'recencio-book-reviews' ) ?>..." style="width: 100%" >
					</textarea>
				</div>
			</div>
		</div>
		<div class="finished">
			<button class="button button-primary" id="rcno_currently_reading_update" style="margin-right: 10px;" >
				<?php _e( 'Update Progress', 'recencio-book-reviews' ); ?>
			</button>
			<label for="rcno_currently_reading_finished">
				<input v-model="curr_update.finished_book" name="rcno_currently_reading_finished" id="rcno_currently_reading_finished" type="checkbox" />
				<?php _e( 'Finished', 'recencio-book-reviews' ) ?>
			</label>
				<span v-if="curr_update.last_updated" class="last-updated">
					{{ time_ago }}
				</span>
		</div>
	</form>
</template>

	<style>
		.currently-reading-wrapper input[type=text]:first-child {
			margin: 0 0 10px 0;
			width: 100%;
		}
		.currently-reading-wrapper:after {
			content: "";
			display: block;
			clear: both;
		}
		.currently-reading-wrapper {
			position: relative;
		}
		.currently-reading-wrapper .book {
			width: 100px;
			display: inline-block;
			margin: 0 10px 0 0;
		}
		.currently-reading-wrapper .info {
			display: inline-block;
			float: right;
			width: calc(100% - 110px);
		}
		input#rcno_current_page_number,
		input#rcno_current_num_pages {
			width: 60px !important;
		}
		#rcno_currently_reading_form .finished {
			position: relative;
			margin: 10px 0 0 0;
		}
		#rcno_currently_reading_form .notice {
			padding: 8px;
		}
		span.last-updated {
			float: right;
			font-style: italic;
			font-size: 12px;
			color: #969696;
		}
		.currently-reading-wrapper .book .progress-bar {
			width: 100%;
			height: 20px;
			background: #f1f1f1;
		}
		.currently-reading-wrapper .book .percentage {
			height: 20px;
			background: #0085ba;
		}
		.progress-bar .percentage-value {
			float: right;
			color: #ffffff;
			margin: 0 30% 0 0;
			font-weight: bold;
		}
		.book-upload-container {
			height: 140px;
			background: #f1f1f1;
			text-align: center;
			cursor: pointer;
		}
		.book-upload-container .dashicons {
			font-size: 2em;
			color: #797979;
			margin: 1em 0 0em 0;
		}
		.fade-enter-active, .fade-leave-active {
			transition: opacity .5s;
		}
		.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
			opacity: 0;
		}
		.rcno-currently-loading {
			width: 100%;
			min-height: 255px;
			display: flex;
			justify-content: center;
			align-items: center;
		}
		.lds-ellipsis {
			display: inline-block;
			position: relative;
			width: 64px;
			height: 64px;
		}
		.lds-ellipsis div {
			position: absolute;
			top: 27px;
			width: 11px;
			height: 11px;
			border-radius: 50%;
			background: #f1f1f1;
			animation-timing-function: cubic-bezier(0, 1, 1, 0);
		}
		.lds-ellipsis div:nth-child(1) {
			left: 6px;
			animation: lds-ellipsis1 0.6s infinite;
		}
		.lds-ellipsis div:nth-child(2) {
			left: 6px;
			animation: lds-ellipsis2 0.6s infinite;
		}
		.lds-ellipsis div:nth-child(3) {
			left: 26px;
			animation: lds-ellipsis2 0.6s infinite;
		}
		.lds-ellipsis div:nth-child(4) {
			left: 45px;
			animation: lds-ellipsis3 0.6s infinite;
		}
		@keyframes lds-ellipsis1 {
			0% {
				transform: scale(0);
			}
			100% {
				transform: scale(1);
			}
		}
		@keyframes lds-ellipsis3 {
			0% {
				transform: scale(1);
			}
			100% {
				transform: scale(0);
			}
		}
		@keyframes lds-ellipsis2 {
			0% {
				transform: translate(0, 0);
			}
			100% {
				transform: translate(19px, 0);
			}
		}
	</style>
<?php } ?>
