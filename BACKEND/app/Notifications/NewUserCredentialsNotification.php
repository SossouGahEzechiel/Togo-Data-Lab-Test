<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewUserCredentialsNotification extends Notification implements ShouldQueue
{
	use Queueable;

	public function __construct(
		public string $email,
		public string $password,
		public string $firstName,
		public string $lastName
	) {}

	public function via(object $notifiable): array
	{
		return ['mail'];
	}

	public function toMail(object $notifiable): MailMessage
	{
		$loginUrl = config('app.client_login_url', 'http://localhost:3000/login');

		return (new MailMessage)
			->subject('Vos identifiants de connexion - ' . config('app.name'))
			->greeting("Bonjour {$this->firstName} {$this->lastName},")
			->line('Votre compte a été créé avec succès sur notre plateforme de gestion des réservations de véhicules.')
			->line('Voici vos identifiants de connexion :')
			->line("**Email :** `{$this->email}`")
			->line("**Mot de passe temporaire :** `{$this->password}`")
			->line('')
			->line('**Important :** Pour des raisons de sécurité, nous vous recommandons de :')
			->line('1. Vous connecter dès que possible')
			->line('2. Changer votre mot de passe dans la section "Mon profil"')
			->line('3. Ne jamais partager vos identifiants')
			->action('Se connecter maintenant', $loginUrl)
			->line('')
			->line('**Lien de connexion :**')
			->line($loginUrl)
			->line('')
			->line('Si vous rencontrez des difficultés, n\'hésitez pas à contacter notre support.')
			->salutation('Cordialement,<br>L\'équipe ' . config('app.name'));
	}

	public function toArray(object $notifiable): array
	{
		return [
			'email' => $this->email,
			'first_name' => $this->firstName,
			'last_name' => $this->lastName,
			'password_generated_at' => now()->toDateTimeString()
		];
	}
}
