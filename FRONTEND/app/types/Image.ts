export interface Image {
	id: string;
	path: string;
}

export const pathToTheServer = (path: string) => {
	return import.meta.env.VITE_API_FILE_PATH + path;
};
