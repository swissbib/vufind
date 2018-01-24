import {AxiosResponse, default as Axios} from "axios";
import {BibliographicDetails} from "./BibliographicDetails";
import {Organisation} from "./Organisation";
import {Person} from "./Person";
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

    public getPersonDetails(personIds: string): Promise<Person[]> {
        const config = {
            ...this.axiosConfig,
            method: "get",
            params: {
                "index": "lsb",
                // lookfor: "[" + contributorIds + "]",
                "method": "getAuthors",
                "overrideIds[]": personIds,
                "searcher": "ElasticSearch",
                "type": "person",
            },
        };

        return Axios.request(config)
            .then((response: AxiosResponse) => {
                return response.data as Person[];
            });
    }

    public getOrganisationDetails(organisationIds: string): Promise<Organisation[]> {
        const config = {
            ...this.axiosConfig,
            method: "get",
            params: {
                "index": "lsb",
                // lookfor: "[" + contributorIds + "]",
                "method": "getOrganisations",
                "overrideIds[]": organisationIds,
                "searcher": "ElasticSearch",
                "type": "organisation",
            },
        };

        return Axios.request(config)
            .then((response: AxiosResponse) => {
                return response.data as Organisation[];
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
}
