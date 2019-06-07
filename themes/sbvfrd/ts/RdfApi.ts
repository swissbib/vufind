import {AxiosResponse, default as Axios} from "axios";
import BibliographicDetails from "./BibliographicDetails";
import Organisation from "./Organisation";
import Person from "./Person";
import Subject from "./Subject";

export default class RdfApi {

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


    //todo: Überlege
    //sollen die Methoden eine abweichende Signatur erhalten, damit der ES workflow erhalten bleibt?
    //also, anstelle von
    //getBibliographicResource getBibliographicResourceApi

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
                searchType: "ID_SEARCH_BIB_RESOURCE",
                lookfor: bibliographicResourceId,
                method: "getBibliographicResource",
                searcher: "swissbibrdfdataapi",
            },
        };

        return Axios.request<BibliographicDetails[]>(config)
            .then((response: AxiosResponse): BibliographicDetails => {
                if (response.data.data.length > 0) {
                    return response.data.data[0];
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
                // lookfor: "[" + contributorIds + "]",
                "searchType": "IDS_SEARCH_PERSON",
                "method": "getAuthors",
                "overrideIds[]": personIds,
                "searcher": "swissbibrdfdataapi",
            },
        };

        return Axios.request(config)
            .then((response: AxiosResponse) => {
                return response.data.data as Person[];
            });
    }

    public getOrganisationDetails(organisationIds: string): Promise<Organisation[]> {
        const config = {
            ...this.axiosConfig,
            method: "get",
            params: {
                // lookfor: "[" + contributorIds + "]",
                //todo: brauchen wir die Untereteilung zwischen person und organisation
                "searchType": "IDS_SEARCH_ORGANISATION",
                "method": "getOrganisations",
                "overrideIds[]": organisationIds,
                "searcher": "swissbibrdfdataapi",
            },
        };

        return Axios.request(config)
            .then((response: AxiosResponse) => {
                return response.data.data as Organisation[];
            });
    }

    public getSubjectDetails(subjectIds: string): Promise<Subject[]> {
        const config = {
            ...this.axiosConfig,
            method: "get",
            params: {
                "searchType": "IDS_SEARCH_ORGANISATION",
                // lookfor: "[" + contributorIds + "]",
                "method": "getSubjects",
                "overrideIds[]": subjectIds,
                "searcher": "swissbibrdfdataapi",
            },
        };

        return Axios.request(config)
            .then((response: AxiosResponse) => {
                return response.data.data as Subject[];
            });
    }
}
