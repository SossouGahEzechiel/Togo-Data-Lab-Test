export const formatDate = (dateString: string) => {
	return new Date(dateString).toLocaleDateString("fr-FR", {
		year: "numeric",
		month: "2-digit",
		day: "2-digit",
	});
};
