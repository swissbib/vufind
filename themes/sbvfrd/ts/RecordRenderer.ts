import {AxiosPromise} from "axios";
import * as $ from "jquery";
import BibliographicDetails from "./BibliographicDetails";
import Detail from "./Detail";
import Hydra from "./Hydra";
import Subject from "./Subject";

export default class RecordRenderer {

    protected client: Hydra;

    constructor(dataUrl: string) {
        this.client = new Hydra(dataUrl);
    }

    public renderContributors(id: string, template: any, htmlList: HTMLElement): Promise<HTMLElement[]> {
        return this.client.getBibliographicDetails(id)
            .then((bibliographicDetails: BibliographicDetails) => {
                const personIds = bibliographicDetails.persons;
                const organisationIds = bibliographicDetails.organisations;
                if (!(personIds || organisationIds)) {
                    return;
                }
                $(htmlList).empty();
                const promises: Array<Promise<Detail[]>> = [];
                if (personIds) {
                    promises.push(this.client.getPersonDetails(personIds));
                }
                if (organisationIds) {
                    promises.push(this.client.getOrganisationDetails(organisationIds));
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

    public renderSubjects(subjects: JQuery<HTMLElement>, template: any): Promise<void> {
        let subjectIds: string = "";
        subjects.each((i, el) => {
            subjectIds += "http://d-nb.info/gnd/" + $(el).attr("subjectid") + ",";
        });
        subjectIds = subjectIds.slice(0, -1);
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
