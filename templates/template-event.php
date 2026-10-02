<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$settings = gaps_get_settings();
$privacy_url = gaps_get_privacy_url();
$gallery_ids = array_filter( array_map( 'absint', array(
	$settings['event_gallery_image_1_id'],
	$settings['event_gallery_image_2_id'],
	$settings['event_gallery_image_3_id'],
	$settings['event_gallery_image_4_id'],
) ) );
$single = gaps_get_event_price_cents( 'single' );
$couple = gaps_get_event_price_cents( 'couple' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo esc_html( $settings['event_title'] . ' — ' . $settings['event_partner'] ); ?></title>
	<meta name="description" content="Serata pratica per genitori e caregiver sulla prevenzione e gestione del soffocamento pediatrico. <?php echo esc_attr( $settings['event_date'] . ', ore ' . $settings['event_time'] ); ?>.">
	<?php wp_head(); ?>
</head>
<body class="gaps-event-body">
<main class="gaps-event">
	<section class="gaps-event-hero">
		<div class="gaps-event-shell gaps-event-hero-grid">
			<div>
				<p class="gaps-event-kicker">Formalife × <?php echo esc_html( $settings['event_partner'] ); ?></p>
				<h1><?php echo esc_html( $settings['event_title'] ); ?></h1>
				<p class="gaps-event-lead">Una serata pratica per genitori e caregiver: meno teoria da ricordare sotto stress, più chiarezza su cosa osservare e come comportarsi davanti a un soffocamento.</p>
				<div class="gaps-event-facts">
					<span>📅 <?php echo esc_html( $settings['event_date'] ); ?></span>
					<span>🕠 Ore <?php echo esc_html( $settings['event_time'] ); ?></span>
					<span>📍 <?php echo esc_html( $settings['event_location'] ); ?></span>
					<span>⏱ <?php echo esc_html( $settings['event_duration'] ); ?></span>
				</div>
				<a href="#iscrizione" class="gaps-event-btn">Iscriviti alla serata</a>
			</div>
			<div class="gaps-event-hero-card" aria-label="In breve">
				<strong>Non è un incontro solo da ascoltare.</strong>
				<p>Prevenzione, riconoscimento, domande e una componente pratica guidata. La quota comprende anche <em>La Guida Anti-Panico al Soffocamento Pediatrico</em> come riferimento da tenere a casa.</p>
			</div>
		</div>
	</section>

	<section class="gaps-event-section">
		<div class="gaps-event-shell">
			<p class="gaps-event-eyebrow">Cosa faremo insieme</p>
			<h2>Due ore costruite per essere utili nella vita reale</h2>
			<div class="gaps-event-cards">
				<article><span>01</span><h3>Prevenire</h3><p>Mettiamo ordine nelle situazioni più comuni e nei comportamenti che aiutano a ridurre il rischio.</p></article>
				<article><span>02</span><h3>Riconoscere</h3><p>Chiariremo come distinguere una situazione critica e quali segnali richiedono un’azione immediata.</p></article>
				<article><span>03</span><h3>Provare</h3><p>Una componente pratica guidata permette di trasformare informazioni astratte in una prima esperienza concreta.</p></article>
			</div>
			<p class="gaps-event-science-note">I contenuti dell’incontro sono sottoposti a supervisione scientifica pediatrica. La serata ha finalità educativa e pratica e non costituisce un corso BLSD certificato.</p>
		</div>
	</section>

	<section class="gaps-event-section gaps-event-section--soft">
		<div class="gaps-event-shell">
			<p class="gaps-event-eyebrow">Come lavoriamo</p>
			<h2>Alcuni momenti dai corsi Formalife</h2>
			<p class="gaps-event-copy">Niente immagini patinate di repertorio: qui puoi mostrare attività reali, persone reali e la parte pratica che caratterizza gli incontri Formalife.</p>
			<div class="gaps-event-gallery">
				<?php if ( $gallery_ids ) : ?>
					<?php foreach ( $gallery_ids as $image_id ) : ?>
						<figure><?php echo gaps_render_attachment_image( $image_id, 'large', array( 'alt' => '', 'class' => 'gaps-event-gallery-image', 'sizes' => '(max-width: 760px) 92vw, 46vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></figure>
					<?php endforeach; ?>
				<?php else : ?>
					<?php for ( $i = 0; $i < 4; $i++ ) : ?><div class="gaps-event-gallery-placeholder">Foto corso<br><small>Caricala da Guida Anti-Panico → Impostazioni</small></div><?php endfor; ?>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="gaps-event-section">
		<div class="gaps-event-shell gaps-event-included-grid">
			<div>
				<p class="gaps-event-eyebrow">Cosa è incluso</p>
				<h2>Una serata, più un riferimento che resta a casa</h2>
				<ul class="gaps-event-checklist">
					<li>Incontro di <?php echo esc_html( $settings['event_duration'] ); ?></li>
					<li>Parte informativa e componente pratica guidata</li>
					<li>Spazio per domande e chiarimenti</li>
					<li>Materiali previsti per l’attività</li>
					<li>Una copia de <em>La Guida Anti-Panico al Soffocamento Pediatrico</em></li>
				</ul>
			</div>
			<div class="gaps-event-price-card">
				<div><span>1 persona</span><strong><?php echo esc_html( number_format_i18n( $single / 100, 2 ) ); ?> €</strong></div>
				<div><span>Coppia</span><strong><?php echo esc_html( number_format_i18n( $couple / 100, 2 ) ); ?> €</strong></div>
				<a href="#iscrizione" class="gaps-event-btn gaps-event-btn--full">Conferma il posto</a>
				<small>Pagamento sicuro gestito da Stripe.</small>
			</div>
		</div>
	</section>

	<section class="gaps-event-section gaps-event-section--faq">
		<div class="gaps-event-shell gaps-event-narrow">
			<p class="gaps-event-eyebrow">Domande frequenti</p>
			<h2>Prima di iscriverti</h2>
			<details><summary>Serve avere già esperienza?</summary><p>No. L’incontro è pensato per genitori e caregiver e parte dalle basi necessarie per comprendere il problema.</p></details>
			<details><summary>È un corso BLSD certificato?</summary><p>No. È una serata educativa e pratica dedicata alla prevenzione e alla gestione del soffocamento pediatrico. Non rilascia una certificazione BLSD.</p></details>
			<details><summary>Posso partecipare in coppia?</summary><p>Sì. La formula coppia consente l’iscrizione di due partecipanti allo stesso evento.</p></details>
			<details><summary>Cosa comprende la quota?</summary><p>L’incontro, la componente pratica prevista, i materiali e una copia de <em>La Guida Anti-Panico al Soffocamento Pediatrico</em>.</p></details>
			<details><summary>Quanto dura?</summary><p><?php echo esc_html( ucfirst( $settings['event_duration'] ) ); ?>.</p></details>
			<details><summary>Come viene confermata l’iscrizione?</summary><p>Compili il modulo qui sotto e completi il pagamento con Stripe. Dopo la conferma del pagamento ricevi l’email di iscrizione confermata.</p></details>
		</div>
	</section>

	<section id="iscrizione" class="gaps-event-section gaps-event-section--checkout">
		<div class="gaps-event-shell gaps-event-checkout-grid">
			<div>
				<p class="gaps-event-eyebrow">Iscrizione</p>
				<h2>Conferma la tua partecipazione</h2>
				<p>Inserisci i dati del partecipante, scegli la formula e completa il pagamento. Formalife gestisce direttamente iscrizione e pagamento.</p>
			</div>
			<form id="gaps-event-registration-form" class="gaps-event-form" novalidate>
				<div class="gaps-event-form-row"><label>Nome<input name="nome" type="text" autocomplete="given-name" required></label><label>Cognome<input name="cognome" type="text" autocomplete="family-name" required></label></div>
				<div class="gaps-event-form-row"><label>Email<input name="email" type="email" autocomplete="email" required></label><label>Telefono<input name="telefono" type="tel" autocomplete="tel" required></label></div>
				<fieldset class="gaps-event-ticket-picker"><legend>Scegli la formula</legend>
					<label><input type="radio" name="ticket_type" value="single" checked><span><strong>1 persona</strong><b><?php echo esc_html( number_format_i18n( $single / 100, 2 ) ); ?> €</b></span></label>
					<label><input type="radio" name="ticket_type" value="couple"><span><strong>Coppia</strong><b><?php echo esc_html( number_format_i18n( $couple / 100, 2 ) ); ?> €</b></span></label>
				</fieldset>
				<label id="gaps-event-second-participant-wrap" hidden>Nome e cognome del secondo partecipante<input name="second_participant" type="text" autocomplete="off"></label>
				<label class="gaps-event-privacy"><input type="checkbox" name="privacy" value="1" required> <span>Ho letto l’<?php if ( $privacy_url ) : ?><a href="<?php echo esc_url( $privacy_url ); ?>" target="_blank" rel="noopener">informativa privacy</a><?php else : ?>informativa privacy<?php endif; ?> e acconsento al trattamento dei dati necessario a gestire l’iscrizione.</span></label>
				<div class="gaps-event-total">Totale: <strong id="gaps-event-total"></strong></div>
				<button type="button" id="gaps-event-continue" class="gaps-event-btn gaps-event-btn--full">Continua al pagamento</button>
				<div id="gaps-event-payment-wrap" hidden>
					<div id="gaps-event-payment-loading">Preparazione del pagamento…</div>
					<div id="gaps-event-payment-element"></div>
					<button type="button" id="gaps-event-pay" class="gaps-event-btn gaps-event-btn--full" disabled>Paga e conferma l’iscrizione</button>
				</div>
				<p id="gaps-event-form-message" class="gaps-event-form-message" role="status" aria-live="polite"></p>
			</form>
		</div>
	</section>
</main>
<?php wp_footer(); ?>
</body>
</html>
