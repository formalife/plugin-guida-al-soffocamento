<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$settings = gaps_get_settings();
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php esc_html_e( 'Iscrizione ricevuta — Formalife', 'guida-antipanico-soffocamento' ); ?></title>
	<?php wp_head(); ?>
</head>
<body class="gaps-event-body gaps-event-thankyou-body">
<main class="gaps-event-thankyou">
	<div class="gaps-event-shell gaps-event-narrow">
		<div class="gaps-event-thankyou-card">
			<div class="gaps-event-success-mark">✓</div>
			<p class="gaps-event-eyebrow">Pagamento completato</p>
			<h1>Iscrizione ricevuta</h1>
			<p class="gaps-event-lead">Se Stripe ha confermato il pagamento, riceverai a breve l’email con la conferma definitiva e il riepilogo.</p>
			<div class="gaps-event-facts gaps-event-facts--stacked">
				<span>📅 <?php echo esc_html( $settings['event_date'] ); ?></span>
				<span>🕠 Ore <?php echo esc_html( $settings['event_time'] ); ?></span>
				<span>📍 <?php echo esc_html( $settings['event_location'] ); ?></span>
			</div>
			<p>Conserva l’email di conferma: è il riferimento per la tua iscrizione.</p>
			<?php if ( ! empty( $settings['contact_email'] ) ) : ?><p class="gaps-event-help">Hai bisogno di aiuto? <a href="mailto:<?php echo esc_attr( $settings['contact_email'] ); ?>"><?php echo esc_html( $settings['contact_email'] ); ?></a></p><?php endif; ?>
		</div>
	</div>
</main>
<?php wp_footer(); ?>
</body>
</html>
