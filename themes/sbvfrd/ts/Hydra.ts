import {AxiosPromise, AxiosResponse, default as Axios} from "axios";
// import Promise from "ts-promise";
import * as $ from "jquery";

declare global {
    let VuFind : any;
}

export class Hydra {
    constructor(dataSwissbibUrl: string) {
        this.dataSwissbibUrl = dataSwissbibUrl;
        this.axiosConfig = {
            baseURL: dataSwissbibUrl,
            //timeout: 100000,
            headers: {"Accept": "application/ld+json"}
        }
    }

    private dataSwissbibUrl: string;
    private axiosConfig: Object;

    /**
     * Fetches array with urls of all contributors
     *
     * @param {string} bibliographicResourceId
     * @returns {Promise<string[]>}
     */
    public getContributorUrls(bibliographicResourceId: string): Promise<string[]> {
        return Axios.get<string[]>(this.dataSwissbibUrl + "bibliographicResource/" + bibliographicResourceId, this.axiosConfig)
            .then((response: AxiosResponse): string[] => {
                return response.data.contributor;
            });
    }

    /**
     * Fetches array with details of all contributors
     * @param {string[]} contributorUrls
     * @returns {AxiosPromise<any>[]}
     */
    public getContributorDetails(contributorUrls: string[]): Promise<object>[] {
        let promises: AxiosPromise<any>[] = [];
        for (let url of contributorUrls) {
            promises.push(Axios.get(url, this.axiosConfig).then(response => {
                return response.data;
            }))
        }
        return promises;
    }

    public getContributorDetail(contributorUrl: string): Promise<any> {
        return Axios.get(contributorUrl, this.axiosConfig);
    }

    public getContributorHtml(contributorPromise: Promise<object>): Promise<String> {
        return contributorPromise.then(person => {
            let p : any = person;
            return `<li><a href="${VuFind.path}/Search/Results?lookfor=${p.lastName}, ${p.firstName}&amp;type=Author" title=" ${p.lastName}, ${p.firstName}">${p.lastName}, ${p.firstName}</a> <span ${ this.personHasSufficientData(p) ? ' class="fa fa-info-circle fa-lg"' : '' } style="display:inline;" authorid="${p["@id"]}"></span></li>`;
        });
    }

    /**
     * Should be more than:
     * "@context, @id, @type, id, firstName, lastName, label"
     */
    public personHasSufficientData(data: Object) {
        let len = Object.keys(data).length;
        if (len < 8) {
            console.info(`Has only ${len} keys `, data);
            return false;
        } else {
            return true;
        }
    }
}

$(document).ready(function () {
    console.info('Start author');
    const client = new Hydra("http://data.swissbib.ch/");
    const recordIdEl: HTMLInputElement = $("input#record_id")[0] as HTMLInputElement;


    client.getContributorUrls(recordIdEl.value)
        .then((urls: string[]) => {
            return client.getContributorDetails(urls);
        }).then((contributors) => {
            let list: HTMLElement = $(".sidebar .list-group")[0];
            //$(sidebar).toggleClass("invisible");
            //let list: JQuery<HTMLElement> = $(sidebar).append("<ul class=\"list-group\"></ul>");

            for (let contributor of contributors) {
                client.getContributorHtml(contributor).then((html: string) => {
                    console.info(html);
                    // let el : JQuery<HTMLElement> = $(list).append(html);
                    let el = $(html).appendTo(list);
                    el.addClass("list-group-item");
                });
            }
    })

});