import React, {Component} from 'react'

export const MandatoryContext = React.createContext();

/*
 * Mandatory Context is used
 * @ ProfileTabs/SkillTab.js
 * @ ProfileTabs/MandtoryBlock/index.js
 */
export default function (WrappedRoutes) {
    return class WithContext extends Component {
        constructor(props) {
            super(props)

            this.state = {
                mandatory_skills: [], //getter of mandatory_skills context
                updateMandtorySkills: this.updateMandtorySkills,  //setter
                fieldData: {}, //getter
                updateFieldData: this.updateFieldData //setter
            }
        }

        updateMandtorySkills = (mandatory_skills) => {
            this.setState({mandatory_skills})
        }

        updateFieldData = (fieldData) => {
            this.setState({fieldData})
        }


        render() {
            return (
                <MandatoryContext.Provider value={this.state}>
                    <WrappedRoutes {...this.props} />
                </MandatoryContext.Provider>
            )
        }
    }
}

// {
//   mandatory_skills:this.state.mandatory_skills,
//   updateMandtorySkills:this.updateMandtorySkills,
//   fieldData:this.state.fieldData,
//   updateFieldData:this.updateFieldData
//   }