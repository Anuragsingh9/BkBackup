import React from "react";
import "../GridComponent.css";
import {useTranslation} from "react-i18next";
import {connect} from "react-redux";

let EmptyGrid = (props) => {
    const {t} = useTranslation("grid");

    return (
        <>
            {
                props.allowNetworking && props.gridPagination.currentPageData <= 1
                    ? <p className="less_people_in_grid">
                        {t("single people in grid")}
                    </p>
                    : <></>
            }
        </>
    );
};

const mapStateToProps = (state) => {
    return {
        allowNetworking: state.Dashboard.networkingState.allowed,
        gridPagination: state.NewInterface.gridPagination,
    };
};

EmptyGrid = connect(mapStateToProps)(EmptyGrid);
export default EmptyGrid;



