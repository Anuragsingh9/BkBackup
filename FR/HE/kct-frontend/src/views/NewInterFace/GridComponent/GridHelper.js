const prepareRowsByConversations = (conversations, maxColumn) => {
    let rows = [[]];
    let currentColumnUsed = 0;
    let currentRow = 0;

    let addConversationInRow = (conversation, usersCount) => {
        let seatAvailableInRow = maxColumn - currentColumnUsed;


        if (seatAvailableInRow < usersCount) {
            // available seats are less in current row, so increasing row
            currentRow++;
            currentColumnUsed = 0;
            rows[currentRow] = [];
        }
        rows[currentRow].push(conversation);
        currentColumnUsed += usersCount;
    }

    conversations.forEach(conversation => {
        let skipConversation = false;
        conversation.conversation_users = conversation.conversation_users.filter(conversationUser => {
            if(conversationUser.is_self === 1) {
                // this is self conversation so skipping
                skipConversation = true;
            }
            if(conversationUser.is_host === 1) {
                // this is host user, so just skipping host only from conversation
                return false;
            }
            return true;
        });
        if(skipConversation) return;

        let usersCount = conversation.conversation_users?.length || 0;

        // if there is no more user in conversation then skipping
        // this may happen when space host is alone and this iteration has removed the space host user from this
        // conversation so removing it
        if(!usersCount) return;

        addConversationInRow(conversation, usersCount)

    })
    return rows;
}

let GridHelper = {
    prepareRowsByConversations: prepareRowsByConversations,
}

export default GridHelper;