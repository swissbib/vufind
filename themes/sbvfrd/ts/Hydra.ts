import {AxiosPromise, AxiosResponse, default as Axios} from "axios";
import * as $ from "jquery";

export class Hydra {

    /**
     * Should be more than:
     * "@context, @id, @type, id, firstName, lastName, label"
     */
    public static personHasSufficientData(data: object): boolean {
        const len = Object.keys(data).length;
        if (len > 4) {
            return true;
        } else {
            return false;
        }
    }

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

    public renderContributors(bibliographicResourceId: string,
                              htmlList: HTMLElement,
                              template: any): Promise<HTMLElement> {
        return this
            .getContributorIds(bibliographicResourceId)
            .then((ids: string) => {
                return this.getContributorDetails(ids);
            })
            .then((contributors: AxiosResponse<any[]>) => {
                for (const p of contributors.data) {
                    $(template(p)).appendTo(htmlList);
                }
                return htmlList;
            });
    }

    /**
     * Fetches array with ids of all contributors
     *
     * @param {string} bibliographicResourceId
     * @returns {Promise<string[]>}
     */
    public getContributorIds(bibliographicResourceId: string): Promise<string> {
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

        return Axios.request<string>(config)
            .then((response: AxiosResponse): string => {
                return response.data[0].contributors;
            });
    }

    public getContributorDetails(contributorIds: string): AxiosPromise<object[]> {
        const config = {
            ...this.axiosConfig,
            method: "get",
            params: {
                lookfor: "[" + contributorIds + "]",
                method: "getAuthor",
                searcher: "ElasticSearch",
                type: "person",
            },
        };

        return Axios.request(config)
            .then((response: AxiosResponse) => {
                return response;
            });
    }

    public getContributorHtml(contributorPromise: Promise<object>, template: any): AxiosPromise<string> {
        return contributorPromise
            .then((person) => {
                const p: any = person;
                return template(p);
            });
    }

}
