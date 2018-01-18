import {AxiosPromise} from "axios";
import * as $ from "jquery";
import {BibliographicDetails} from "./BibliographicDetails";
import {Detail} from "./Detail";
import {Hydra} from "./Hydra";
import {Subject} from "./Subject";

export class RecordRenderer {

    protected client: Hydra;

    constructor(dataUrl: string) {
        this.client = new Hydra(dataUrl);
    }

    public render(id: string, template: any, htmlList: HTMLElement): Promise<HTMLElement[]> {
        return this.client.getBibliographicDetails(id)
            .then((bibliographicDetails: BibliographicDetails) => {
                const promises: Array<Promise<Detail[]>> = [];
                const contributorIds = bibliographicDetails.contributors;
                if (contributorIds && contributorIds.length > 0) {
                    promises.push(this.client.getContributorDetails(contributorIds));
                }
                return Promise.all(promises)
                    .then((details: Detail[][]) => {
                        const elements: HTMLElement[] = [];
                        for (const detail of details) {
                            elements.push(
                                this.renderDetails(detail, template, htmlList),
                            );
                        }
                        return elements;
                    });
            });
    }

    public renderDetails(items: Detail[], template: any, htmlList: HTMLElement): HTMLElement {
        for (const p of items) {
            $(template(p)).appendTo(htmlList);
        }
        return htmlList;
    }

    public getContributorHtml(contributorPromise: Promise<Detail>, template: any): AxiosPromise<string> {
        return contributorPromise
            .then((person) => {
                const p: any = person;
                return template(p);
            });
    }

    public renderSubjects(subjects: JQuery<HTMLElement>, template: any) {
        let subjectIds: string = "";
        subjects.each((i, el) => {
            subjectIds += "http://d-nb.info/gnd/" + $(el).attr("subjectid") + ",";
        });
        const subjectDetails: Promise<Subject[]> = this.client.getSubjectDetails(subjectIds);
        return subjectDetails
            .then((details: Subject[]) => {
                details.forEach((detail: Subject) => {
                    if (detail.hasSufficientData) {
                        const li = subjects.filter("[subjectid='" + detail.id + "']");
                        li.append(template(detail));
                    }
                });
            });
    }
}
