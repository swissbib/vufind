import {AxiosResponse, default as Axios} from "axios";
import {BibliographicDetails} from "./BibliographicDetails";
import {Contributor} from "./Contributor";
import {Subject} from "./Subject";

export class Hydra {

    private apiUrl: string;
    private axiosConfig: object;

    constructor(apiUrl: string) {
        this.apiUrl = apiUrl;
        this.axiosConfig = {
            baseURL: apiUrl,
            // timeout: 100000,
            headers: {Accept: "application/ld+json"},
            url: apiUrl,
        };
    }

    /**
     * Fetches array with ids of all contributors
     *
     * @param {string} bibliographicResourceId
     * @returns {Promise<string[]>}
     */
    public getBibliographicDetails(bibliographicResourceId: string): Promise<BibliographicDetails> {
        const config = {
            ...this.axiosConfig,
            method: "get",
            params: {
                lookfor: bibliographicResourceId,
                method: "getBibliographicResource",
                searcher: "ElasticSearch",
                type: "bibliographicResource",
            },
        };

        return Axios.request<BibliographicDetails[]>(config)
            .then((response: AxiosResponse): BibliographicDetails => {
                if (response.data.length > 0) {
                    return response.data[0];
                } else {
                    return new BibliographicDetails();
                }
            });
    }

    public getContributorDetails(contributorIds: string): Promise<Contributor[]> {
        const config = {
            ...this.axiosConfig,
            method: "get",
            params: {
                "index": "lsb",
                // lookfor: "[" + contributorIds + "]",
                "method": "getAuthors",
                "overrideIds[]": contributorIds,
                "searcher": "ElasticSearch",
                "type": "person",
            },
        };

        return Axios.request(config)
            .then((response: AxiosResponse) => {
                return response.data as Contributor[];
            });
    }

    public getSubjectDetails(subjectIds: string): Promise<Subject[]> {
        const config = {
            ...this.axiosConfig,
            method: "get",
            params: {
                "index": "gnd",
                // lookfor: "[" + contributorIds + "]",
                "method": "getSubjects",
                "overrideIds[]": subjectIds,
                "searcher": "ElasticSearch",
                "type": "DEFAULT",
            },
        };

        return Axios.request(config)
            .then((response: AxiosResponse) => {
                return response.data as Subject[];
            });
    }

    public getDetailsOfSubject(subjectId: string): Promise<Subject> {
        const config = {
            ...this.axiosConfig,
            method: "get",
            params: {
                "index": "gnd",
                // lookfor: "[" + contributorIds + "]",
                "method": "getSubjects",
                "overrideIds[]": subjectId,
                "searcher": "ElasticSearch",
                "type": "DEFAULT",
            },
        };

        return Axios.request(config)
            .then((response: AxiosResponse) => {
                return response.data as Subject;
            });
    }
}
