<?php
/**
 * Gestisce le iscrizioni alle Serate Anti-Panico partner-hosted.
 * Il primo evento configurato è Il Giardino delle Fate — 24/11/2026.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GAPS_Event_Registration {
	const CPT          = 'gaps_event_reg';
	const NONCE_ACTION = 'gaps_event_registration_nonce';
	const AJAX_ACTION  = 'gaps_submit_event_registration';
	const EVENT_KEY    = 'giardino_delle_fate_2026_11_24';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( __CLASS__, 'handle_submit' ) );
		add_action( 'wp_ajax_nopriv_' . self::AJAX_ACTION, array( __CLASS__, 'handle_submit' ) );
		add_filter( 'manage_' . self::CPT . '_posts_columns', array( __CLASS__, 'add_columns' ) );
		add_action( 'manage_' . self::CPT . '_posts_custom_column', array( __CLASS__, 'render_column' ), 10, 2 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
	}

	public static function register_post_type() {
		register_post_type(
			self::CPT,
			array(
				'labels'          => array(
					'name'          => __( 'Iscrizioni eventi', 'guida-antipanico-soffocamento' ),
					'singular_name' => __( 'Iscrizione evento', 'guida-antipanico-soffocamento' ),
					'all_items'     => __( 'Iscrizioni eventi', 'guida-antipanico-soffocamento' ),
					'edit_item'     => __( 'Dettaglio iscrizione', 'guida-antipanico-soffocamento' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => GAPS_SLUG,
				'capability_type' => 'post',
				'map_meta_cap'    => true,
				'supports'        => array( 'title' ),
				'has_archive'     => false,
				'rewrite'         => false,
				'show_in_rest'    => false,
			)
		);
	}

	public static function add_columns( $columns ) {
		return array(
			'cb'                 => $columns['cb'],
			'title'              => __( 'Partecipante', 'guida-antipanico-soffocamento' ),
			'gaps_event'         => __( 'Evento', 'guida-antipanico-soffocamento' ),
			'gaps_ticket'        => __( 'Iscrizione', 'guida-antipanico-soffocamento' ),
			'gaps_event_payment' => __( 'Pagamento', 'guida-antipanico-soffocamento' ),
			'gaps_event_email'   => __( 'Email', 'guida-antipanico-soffocamento' ),
			'gaps_event_phone'   => __( 'Telefono', 'guida-antipanico-soffocamento' ),
			'date'               => $columns['date'],
		);
	}

	public static function render_column( $column, $post_id ) {
		switch ( $column ) {
			case 'gaps_event':
				echo esc_html( get_post_meta( $post_id, '_gaps_event_title', true ) );
				break;
			case 'gaps_ticket':
				$type = get_post_meta( $post_id, '_gaps_event_ticket_type', true );
				echo esc_html( 'couple' === $type ? __( 'Coppia', 'guida-antipanico-soffocamento' ) : __( 'Singolo', 'guida-antipanico-soffocamento' ) );
				break;
			case 'gaps_event_payment':
				echo self::render_payment_badge( get_post_meta( $post_id, '_gaps_payment_status', true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
			case 'gaps_event_email':
				echo esc_html( get_post_meta( $post_id, '_gaps_email', true ) );
				break;
			case 'gaps_event_phone':
				echo esc_html( get_post_meta( $post_id, '_gaps_telefono', true ) );
				break;
		}
	}

	public static function add_meta_box() {
		add_meta_box(
			'gaps_event_registration_details',
			__( 'Dettagli iscrizione evento', 'guida-antipanico-soffocamento' ),
			array( __CLASS__, 'render_meta_box' ),
			self::CPT,
			'normal',
			'high'
		);
	}

	public static function render_meta_box( $post ) {
		$ticket_type  = get_post_meta( $post->ID, '_gaps_event_ticket_type', true );
		$amount_cents = absint( get_post_meta( $post->ID, '_gaps_event_amount_cents', true ) );
		$rows = array(
			__( 'Stato pagamento', 'guida-antipanico-soffocamento' ) => self::render_payment_badge( get_post_meta( $post->ID, '_gaps_payment_status', true ) ),
			__( 'Evento', 'guida-antipanico-soffocamento' ) => esc_html( get_post_meta( $post->ID, '_gaps_event_title', true ) ),
			__( 'Data / ora', 'guida-antipanico-soffocamento' ) => esc_html( trim( get_post_meta( $post->ID, '_gaps_event_date', true ) . ' · ' . get_post_meta( $post->ID, '_gaps_event_time', true ) ) ),
			__( 'Luogo', 'guida-antipanico-soffocamento' ) => esc_html( get_post_meta( $post->ID, '_gaps_event_location', true ) ),
			__( 'Formula', 'guida-antipanico-soffocamento' ) => esc_html( 'couple' === $ticket_type ? __( 'Coppia', 'guida-antipanico-soffocamento' ) : __( 'Singolo', 'guida-antipanico-soffocamento' ) ),
			__( 'Importo', 'guida-antipanico-soffocamento' ) => esc_html( number_format_i18n( $amount_cents / 100, 2 ) . ' €' ),
			__( 'Nome', 'guida-antipanico-soffocamento' ) => esc_html( get_post_meta( $post->ID, '_gaps_nome', true ) ),
			__( 'Cognome', 'guida-antipanico-soffocamento' ) => esc_html( get_post_meta( $post->ID, '_gaps_cognome', true ) ),
			__( 'Secondo partecipante', 'guida-antipanico-soffocamento' ) => esc_html( get_post_meta( $post->ID, '_gaps_second_participant', true ) ),
			__( 'Email', 'guida-antipanico-soffocamento' ) => esc_html( get_post_meta( $post->ID, '_gaps_email', true ) ),
			__( 'Telefono', 'guida-antipanico-soffocamento' ) => esc_html( get_post_meta( $post->ID, '_gaps_telefono', true ) ),
		);

		echo '<table class="widefat"><tbody>';
		foreach ( $rows as $label => $value ) {
			printf( '<tr><th style="width:190px;text-align:left;">%1$s</th><td>%2$s</td></tr>', esc_html( $label ), $value ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		$paid_at = get_post_meta( $post->ID, '_gaps_paid_at', true );
		if ( $paid_at ) {
			printf( '<tr><th style="width:190px;text-align:left;">%1$s</th><td>%2$s</td></tr>', esc_html__( 'Pagato il', 'guida-antipanico-soffocamento' ), esc_html( $paid_at ) );
		}
		echo '</tbody></table>';
	}

	public static function handle_submit() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		$nome = isset( $_POST['nome'] ) ? sanitize_text_field( wp_unslash( $_POST['nome'] ) ) : '';
		$cognome = isset( $_POST['cognome'] ) ? sanitize_text_field( wp_unslash( $_POST['cognome'] ) ) : '';
		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$telefono = isset( $_POST['telefono'] ) ? sanitize_text_field( wp_unslash( $_POST['telefono'] ) ) : '';
		$ticket_type = isset( $_POST['ticket_type'] ) ? sanitize_key( wp_unslash( $_POST['ticket_type'] ) ) : 'single';
		$second_participant = isset( $_POST['second_participant'] ) ? sanitize_text_field( wp_unslash( $_POST['second_participant'] ) ) : '';
		$privacy = isset( $_POST['privacy'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['privacy'] ) );

		if ( ! in_array( $ticket_type, array( 'single', 'couple' ), true ) ) {
			$ticket_type = 'single';
		}
		if ( '' === $nome || '' === $cognome || '' === $telefono || '' === $email || ! is_email( $email ) || ! $privacy ) {
			wp_send_json_error( array( 'message' => __( 'Compila tutti i campi obbligatori e accetta l’informativa privacy.', 'guida-antipanico-soffocamento' ) ) );
		}
		if ( 'couple' === $ticket_type && '' === $second_participant ) {
			wp_send_json_error( array( 'message' => __( 'Inserisci nome e cognome del secondo partecipante.', 'guida-antipanico-soffocamento' ) ) );
		}

		$settings = gaps_get_settings();
		$amount_cents = gaps_get_event_price_cents( $ticket_type );

		$post_id = wp_insert_post(
			array(
				'post_type'   => self::CPT,
				'post_status' => 'publish',
				'post_title'  => trim( $nome . ' ' . $cognome ),
			),
			true
		);
		if ( is_wp_error( $post_id ) || ! $post_id ) {
			wp_send_json_error( array( 'message' => __( 'Non è stato possibile salvare l’iscrizione. Riprova tra poco.', 'guida-antipanico-soffocamento' ) ) );
		}

		$meta = array(
			'_gaps_nome' => $nome,
			'_gaps_cognome' => $cognome,
			'_gaps_email' => $email,
			'_gaps_telefono' => $telefono,
			'_gaps_event_key' => self::EVENT_KEY,
			'_gaps_event_title' => $settings['event_title'],
			'_gaps_event_date' => $settings['event_date'],
			'_gaps_event_time' => $settings['event_time'],
			'_gaps_event_location' => $settings['event_location'],
			'_gaps_event_ticket_type' => $ticket_type,
			'_gaps_second_participant' => $second_participant,
			'_gaps_event_amount_cents' => $amount_cents,
			'_gaps_payment_status' => 'pending',
		);
		foreach ( $meta as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

		$intent = self::create_payment_intent( $post_id, $ticket_type, $email, $amount_cents );
		if ( is_wp_error( $intent ) ) {
			wp_send_json_error( array( 'message' => $intent->get_error_message() ) );
		}

		wp_send_json_success(
			array(
				'client_secret' => $intent['client_secret'],
				'amount_cents' => $intent['amount'],
			)
		);
	}

	private static function create_payment_intent( $post_id, $ticket_type, $email, $amount_cents ) {
		$settings = gaps_get_settings();
		$secret_key = trim( $settings['stripe_secret_key'] );
		if ( '' === $secret_key ) {
			return new WP_Error( 'stripe_not_configured', __( 'Pagamento non disponibile al momento: contatta Formalife.', 'guida-antipanico-soffocamento' ) );
		}

		$body = array(
			'amount' => $amount_cents,
			'currency' => 'eur',
			'receipt_email' => $email,
			'description' => $settings['event_title'] . ' — ' . $settings['event_date'],
			'metadata[wp_event_registration_id]' => $post_id,
			'metadata[event_key]' => self::EVENT_KEY,
			'metadata[ticket_type]' => $ticket_type,
		);
		foreach ( array_values( (array) $settings['stripe_payment_methods'] ) as $index => $method ) {
			$body[ "payment_method_types[{$index}]" ] = sanitize_key( $method );
		}

		$response = wp_remote_post(
			'https://api.stripe.com/v1/payment_intents',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $secret_key,
					'Content-Type' => 'application/x-www-form-urlencoded',
					'Idempotency-Key' => 'gaps-event-registration-' . $post_id,
				),
				'body' => $body,
				'timeout' => 20,
			)
		);

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'stripe_unreachable', __( 'Impossibile contattare Stripe. Riprova tra poco.', 'guida-antipanico-soffocamento' ) );
		}
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $data['client_secret'] ) || empty( $data['id'] ) ) {
			$message = isset( $data['error']['message'] ) ? $data['error']['message'] : __( 'Errore Stripe sconosciuto.', 'guida-antipanico-soffocamento' );
			return new WP_Error( 'stripe_error', $message );
		}

		update_post_meta( $post_id, '_gaps_stripe_payment_intent_id', sanitize_text_field( $data['id'] ) );
		return array( 'client_secret' => $data['client_secret'], 'amount' => $amount_cents );
	}

	public static function process_payment_event( $type, $object ) {
		$post_id = isset( $object['metadata']['wp_event_registration_id'] ) ? absint( $object['metadata']['wp_event_registration_id'] ) : 0;
		if ( ! $post_id || self::CPT !== get_post_type( $post_id ) ) {
			return;
		}

		$already_paid = ( 'paid' === get_post_meta( $post_id, '_gaps_payment_status', true ) );
		if ( 'payment_intent.payment_failed' === $type ) {
			if ( ! $already_paid ) {
				update_post_meta( $post_id, '_gaps_payment_status', 'failed' );
			}
			return;
		}
		if ( 'payment_intent.succeeded' !== $type || $already_paid ) {
			return;
		}

		update_post_meta( $post_id, '_gaps_payment_status', 'paid' );
		update_post_meta( $post_id, '_gaps_paid_at', current_time( 'mysql' ) );
		if ( ! empty( $object['id'] ) ) {
			update_post_meta( $post_id, '_gaps_stripe_payment_intent_id', sanitize_text_field( $object['id'] ) );
		}
		self::notify_internal( $post_id, $object );
		self::notify_customer( $post_id, $object );
	}

	private static function notify_internal( $post_id, $object ) {
		$settings = gaps_get_settings();
		$to = '' !== trim( $settings['notify_email'] ) ? $settings['notify_email'] : get_option( 'admin_email' );
		$name = trim( get_post_meta( $post_id, '_gaps_nome', true ) . ' ' . get_post_meta( $post_id, '_gaps_cognome', true ) );
		$type = get_post_meta( $post_id, '_gaps_event_ticket_type', true );
		$amount = isset( $object['amount'] ) ? number_format_i18n( absint( $object['amount'] ) / 100, 2 ) . ' €' : '';
		$subject = sprintf( __( 'Iscrizione pagata — %s', 'guida-antipanico-soffocamento' ), $name );
		$body = __( 'Stripe ha confermato il pagamento di un’iscrizione evento.', 'guida-antipanico-soffocamento' ) . "

";
		$body .= $settings['event_title'] . "
" . $settings['event_date'] . ' · ' . $settings['event_time'] . "
" . $settings['event_location'] . "

";
		$body .= __( 'Partecipante:', 'guida-antipanico-soffocamento' ) . ' ' . $name . "
";
		if ( 'couple' === $type ) {
			$body .= __( 'Secondo partecipante:', 'guida-antipanico-soffocamento' ) . ' ' . get_post_meta( $post_id, '_gaps_second_participant', true ) . "
";
		}
		$body .= __( 'Email:', 'guida-antipanico-soffocamento' ) . ' ' . get_post_meta( $post_id, '_gaps_email', true ) . "
";
		$body .= __( 'Telefono:', 'guida-antipanico-soffocamento' ) . ' ' . get_post_meta( $post_id, '_gaps_telefono', true ) . "
";
		$body .= __( 'Formula:', 'guida-antipanico-soffocamento' ) . ' ' . ( 'couple' === $type ? __( 'Coppia', 'guida-antipanico-soffocamento' ) : __( 'Singolo', 'guida-antipanico-soffocamento' ) ) . "
";
		if ( $amount ) {
			$body .= __( 'Importo:', 'guida-antipanico-soffocamento' ) . ' ' . $amount . "
";
		}
		$body .= "
" . admin_url( 'post.php?post=' . $post_id . '&action=edit' );
		wp_mail( $to, $subject, $body );
	}

	private static function notify_customer( $post_id, $object ) {
		$email = get_post_meta( $post_id, '_gaps_email', true );
		if ( ! is_email( $email ) ) {
			return;
		}
		$settings = gaps_get_settings();
		$nome = get_post_meta( $post_id, '_gaps_nome', true );
		$type = get_post_meta( $post_id, '_gaps_event_ticket_type', true );
		$amount = isset( $object['amount'] ) ? number_format_i18n( absint( $object['amount'] ) / 100, 2 ) . ' €' : '';
		$subject = __( 'Iscrizione confermata — Serata Anti-Panico', 'guida-antipanico-soffocamento' );
		$body = sprintf( __( 'Ciao %s,', 'guida-antipanico-soffocamento' ), $nome ) . "

";
		$body .= __( 'il pagamento è andato a buon fine e la tua iscrizione è confermata.', 'guida-antipanico-soffocamento' ) . "

";
		$body .= strtoupper( $settings['event_title'] ) . "
";
		$body .= $settings['event_date'] . ' · ore ' . $settings['event_time'] . "
";
		$body .= $settings['event_location'] . "
";
		$body .= __( 'Durata:', 'guida-antipanico-soffocamento' ) . ' ' . $settings['event_duration'] . "
";
		$body .= __( 'Iscrizione:', 'guida-antipanico-soffocamento' ) . ' ' . ( 'couple' === $type ? __( 'coppia', 'guida-antipanico-soffocamento' ) : __( 'singola', 'guida-antipanico-soffocamento' ) ) . "
";
		if ( $amount ) {
			$body .= __( 'Totale pagato:', 'guida-antipanico-soffocamento' ) . ' ' . $amount . "
";
		}
		$body .= "
" . __( 'La quota comprende l’incontro, la componente pratica prevista, i materiali e La Guida Anti-Panico al Soffocamento Pediatrico.', 'guida-antipanico-soffocamento' ) . "
";
		$body .= "
" . __( 'Conserva questa email come conferma dell’iscrizione.', 'guida-antipanico-soffocamento' ) . "
";
		if ( ! empty( $settings['contact_email'] ) ) {
			$body .= "
" . __( 'Per qualsiasi necessità:', 'guida-antipanico-soffocamento' ) . ' ' . $settings['contact_email'] . "
";
		}
		$headers = array();
		if ( ! empty( $settings['notify_email'] ) && is_email( $settings['notify_email'] ) ) {
			$headers[] = 'From: Formalife <' . $settings['notify_email'] . '>';
		}
		wp_mail( $email, $subject, $body, $headers );
	}

	private static function render_payment_badge( $status ) {
		$labels = array(
			'pending' => array( '⏳ In attesa', '#8a6d3b', '#fdf3d7' ),
			'paid' => array( '✅ Pagato', '#1a7a3e', '#dcf5e3' ),
			'failed' => array( '❌ Fallito', '#9b1c1c', '#fbdcdc' ),
		);
		$status = isset( $labels[ $status ] ) ? $status : 'pending';
		$info = $labels[ $status ];
		return sprintf( '<span style="display:inline-block;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:600;color:%1$s;background:%2$s;">%3$s</span>', esc_attr( $info[1] ), esc_attr( $info[2] ), esc_html( $info[0] ) );
	}
}
