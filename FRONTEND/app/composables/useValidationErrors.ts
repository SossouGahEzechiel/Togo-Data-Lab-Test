export interface ValidationErrors extends Record<string, string | undefined> { }

interface ApiError {
  data?: {
    code?: number;
    message?: string;
    // Pour les erreurs de validation Laravel : { "field": ["message1", "message2"] }
    errors?: Record<string, string[]>;
    // Pour les erreurs 422 anciennes, `data` est un tableau de problèmes de validation.
    // Pour les erreurs 500, `data` peut être null.
    data?: { path: string; msg: string }[] | null;
  };
}

/**
 * Extrait les erreurs de validation d'une réponse d'erreur de l'API.
 * Gère les erreurs de validation Laravel (format errors) et les anciennes erreurs (422) et les erreurs serveur (500).
 * @param error L'objet d'erreur de l'API, potentiellement `null` ou `undefined`.
 * @returns Un `Record<string, string>` des erreurs. Pour les erreurs 500, une clé `_general` est utilisée.
 */
export const useValidationErrors = (error: ApiError | any): ValidationErrors => {
  const validationErrors: ValidationErrors = {};

  // Format Laravel standard : { "errors": { "field": ["message1", "message2"] } }
  const errors = error?.data?.errors;
  if (error?.data?.message && errors && typeof errors === 'object') {
    for (const [field, messages] of Object.entries(errors)) {
      // Prendre le premier message d'erreur pour chaque champ
      validationErrors[field] = Array.isArray(messages) ? messages[0] : messages;
    }
  }
  // Erreurs de validation anciennes (422)
  else if (error?.data?.code === 422 && Array.isArray(error.data.data)) {
    for (const err of error.data.data) {
      if (err.path && err.msg) {
        validationErrors[err.path] = err.msg;
      }
    }
  }
  // Erreurs serveur (500) ou autres erreurs avec un message
  else if (error?.data?.message) {
    // Utilise une clé spéciale pour les erreurs générales non liées à un champ.
    validationErrors._general = error.data.message;
  }

  return validationErrors;
};