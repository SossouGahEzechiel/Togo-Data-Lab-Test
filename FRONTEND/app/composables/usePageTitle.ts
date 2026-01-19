export const usePageTitle = () => {
	const pageTitle = useState("pageTitle", () => "Tableau de bord");
	const pageDescription = useState("pageDescription", () => "");

	const setPageTitle = (title: string, description?: string) => {
		pageTitle.value = title;
		if (description) pageDescription.value = description;
	};

	return { pageTitle, pageDescription, setPageTitle };
};
