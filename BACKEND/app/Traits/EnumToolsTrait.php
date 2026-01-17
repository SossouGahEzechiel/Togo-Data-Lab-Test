<?php

namespace App\Traits;

use Illuminate\Support\Collection;

/**
 * Un trait qui fournit des outils utiles pour manipuler des énumérations PHP.
 */
trait EnumToolsTrait
{
	/**
	 * Récupère toutes les valeurs de l'énumération sous forme de tableau.
	 *
	 * @return array
	 */
	public static function values(): array
	{
		$values = [];
		foreach (self::cases() as $case) {
			$values[] = $case->value;
		}
		return $values;
	}

	/**
	 * Retourne une collection contenant toutes les instances de l'énumération
	 * sauf celles dont les clés sont fournies.
	 *
	 * @param iterable|int $keys Les clés à exclure.
	 * @return Collection
	 */
	public static function except(iterable|int $keys): Collection
	{
		return self::all()->except($keys);
	}

	/**
	 * Retourne une collection contenant uniquement les instances de l'énumération
	 * dont les clés sont spécifiées.
	 *
	 * @param iterable|int $keys Les clés à inclure.
	 * @return Collection
	 */
	public static function only(iterable|int $keys): Collection
	{
		return self::all()->only($keys);
	}

	/**
	 * Retourne une collection de toutes les instances de l'énumération.
	 *
	 * @return Collection
	 */
	public static function all(): Collection
	{
		return collect(self::cases());
	}

	/**
	 * Retourne le nom de l'instance actuelle en minuscule et en format camel case.
	 *
	 * @return string
	 */
	public function camelLowerName(): string
	{
		return str($this->name)->lower()->camel()->toString();
	}

	/**
	 * Retourne le nom de l'instance actuelle formaté en slug avec le séparateur spécifié.
	 *
	 * @param string $separator Le séparateur à utiliser dans le slug.
	 * @return string
	 */
	public function slugged(string $separator = '_'): string
	{
		return str($this->name)->slug($separator)->toString();
	}
}
