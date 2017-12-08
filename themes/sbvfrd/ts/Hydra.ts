import {AxiosPromise, AxiosResponse, default as Axios} from "axios";
import * as $ from "jquery";

declare global {
    let VuFind: any;
}

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

    private dataSwissBibUrl: string;
    private axiosConfig: object;

    constructor(dataSwissbibUrl: string) {
        this.dataSwissBibUrl = dataSwissbibUrl;
        this.axiosConfig = {
            baseURL: dataSwissbibUrl,
            // timeout: 100000,
            headers: {Accept: "application/ld+json"},
            url: dataSwissbibUrl,
        };
    }

    public renderContributorsOld(bibliographicResourceId: string,
                                 htmlList: HTMLElement,
                                 template: any): Promise<HTMLElement> {
        return this
            .getContributorUrls(bibliographicResourceId)
            .then((urls: string[]) => {
                return this.getContributorDetailsOld(urls);
            })
            .then((contributors: Array<AxiosPromise<object>>) => {
                return Promise.all(contributors)
                    .then((contributor: object[]) => {
                        for (const p of contributor) {
                            $(template(p)).appendTo(htmlList);
                        }
                        return htmlList;
                    });
            });
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
     * Fetches array with urls of all contributors
     *
     * @param {string} bibliographicResourceId
     * @returns {Promise<string[]>}
     */
    public getContributorUrls(bibliographicResourceId: string): Promise<string[]> {
        const url = this.dataSwissBibUrl + "bibliographicResource/" + bibliographicResourceId;
        return Axios.get<string[]>(url, this.axiosConfig)
            .then((response: AxiosResponse): string[] => {
                return response.data.contributor;
            });
    }

    /**
     * Fetches array with urls of all contributors
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

    /**
     * Fetches array with details of all contributors
     * @param {string[]} contributorUrls
     * @returns {AxiosPromise<any>[]}
     */
    public getContributorDetailsOld(contributorUrls: string[]): Array<AxiosPromise<object>> {
        const promises: Array<AxiosPromise<object>> = [];
        for (const url of contributorUrls) {
            promises.push(Axios.get(url, this.axiosConfig)
                .then((response: AxiosResponse) => {
                    return response.data;
                }));
        }
        return promises;
    }

    /**
     * @deprecated Not used?
     * @param {string} contributorUrl
     * @returns {Promise<any>}
     */
    public getContributorDetail(contributorUrl: string): AxiosPromise<any> {
        return Axios.get(contributorUrl, this.axiosConfig);
    }

    public getContributorHtml(contributorPromise: Promise<object>, template: any): AxiosPromise<string> {
        return contributorPromise
            .then((person) => {
                const p: any = person;
                return template(p);
            });
    }

}
