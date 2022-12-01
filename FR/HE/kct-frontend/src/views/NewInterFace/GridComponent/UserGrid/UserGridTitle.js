import React, {useEffect, useState} from "react";
import "../GridComponent.css";
import {useTranslation} from "react-i18next";
import _ from 'lodash';

let UserGridTitle = (props) => {
    const {t} = useTranslation("grid");
    const {currentUser} = props;
    const [companyLine, setCompanyLine] = useState('');
    const [unionLine, setUnionLine] = useState('');
    const [tagsLine, setTagsLine] = useState('');

    useEffect(() => {
        if (props.currentUser) {
            let unionLength = currentUser?.unions?.length;

            const tags = [];
            if (currentUser?.tags_data && currentUser?.tags_data?.used_tag) {
                currentUser.tags_data.used_tag.forEach((tag) => tags.push(`#${tag.name}`));
            }
            currentUser.personal_tags.forEach((per_tag) => tags.push(`#${per_tag.name}`));
            currentUser.professional_tags.forEach((pro_tag) => tags.push(`#${pro_tag.name}`));

            setCompanyLine(prepareEntityLine(currentUser?.company));
            unionLength && setUnionLine(prepareEntityLine(currentUser?.unions[unionLength]));
            setTagsLine(tags.join(', '));

        } else {
            setCompanyLine('');
            setUnionLine('');
            setTagsLine('');
        }
    }, [props.currentUser]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check the entity data and prepare the line to show in grid
     * this method checks if entity is present then it will return its name and its position (position will be double
     * checked after entity name)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param entity
     * @returns {string|string}
     */
    const prepareEntityLine = entity => {
        return _.isEmpty(entity)
            ? ""
            : `${entity?.long_name} ${entity?.position ? entity.position : ''}`;
    }

    const gridClass = 'justify-content-center text-center grid_title_line';

    return !props.currentUser ? <>
            <div className={gridClass}>{" "}{t("GRID_TITLE_LINE1")}</div>
            <div className={gridClass}>{" "}{t("GRID_TITLE_LINE2")}</div>
        </>
        : <>
            <div className={gridClass}>
                <strong> {`${currentUser.user_fname} ${currentUser.user_lname}`} </strong>
                {companyLine}
                {unionLine}
            </div>
            <div className={`${gridClass} two_line_tags`}>
                {tagsLine || <>&nbsp;</>}
            </div>
        </>
};

export default UserGridTitle;