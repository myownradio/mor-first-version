var langMessages = {
    'validator' : {
        'LOGIN_TOO_SHORT'               : "Login must contain at least 3 symbols",
        'PASSWORD_TOO_SHORT'            : "Password must contain at least 6 symbols",
        'INCORRECT_EMAIL'               : "Typed email has incorrect format",
        'PASSWORD_NOT_MATCH'            : "Passwords not match"
    },
    'common' : {
        'ERROR_UNAUTHORIZED'            : "You are unauthorized",
        'ERROR_NOT_FOUND'               : "Document not found",
        'ERROR_WRONG_PERMISSION'        : "No access to this object"
    },
    'user' : {
        'SESSION_LOGIN_SUCCESS'         : "User successfully logged in by session",
        'SESSION_LOGIN_FAILED'          : "Unable to login by session. Incorrect login or password",
        
        'LOGIN_SUCCESS'                 : "Successfully logged in",
        'LOGIN_FAILED'                  : "Login failed. Incorrect login or password",
        
        'REG_MESSAGE_SENT'              : "Registration letter sent.",
        'REG_MESSAGE_DIDNT_SEND'        : "Registration letter wasn't sent.",
        'REG_ERROR_EMAIL_EXISTS'        : "User with this email already exists.",
        'REG_ERROR_INCORRECT_EMAIL'     : "Email has incorrect format. Retype it.",
        'REG_ERROR_NOT_ENOUTH_PARAMS'   : "Not all fields was filled correctly.",
        'REG_ERROR_USER_EXISTS'         : "Registration error. User already exists.",
        'REG_COMPLETE'                  : "Registration successfully completed.",
        'REG_ERROR_PASSWORDS_MISMATCH'  : "Passwords mismatch.",
        'REG_ERROR_SHORT_LOGIN'         : "Minimal length of login is 3 symbols",
        'REG_ERROR_SHORT_PASSWORD'      : "Minimal length of password is 3 symbols",
        
        'CODE_CORRECT'                  : "Code accepted",
        'CODE_INCORRECT'                : "Code rejected"
    },
    'track' : {
        'UPDATE_ERROR_TRACK_NOT_EXISTS' : "Can't update info. File not exists.",
        'UPDATE_ERROR_NOT_OWNER'        : "Can't update info. User isn't owner of this file.",
        'UPDATE_SUCCESS'                : "Information updated successfully.",
        'UPDATE_NOT_MODIFIED'           : "Information wasn't changed.",
        
        'DELETE_SUCCESS'                : "Track successfully deleted.",
        'NOTHING_DELETED'               : "No tracks has been deleted.",
        'DELETE_PARTIAL'                : "Some of files hasn't deleted.",
        
        'UPLOAD_ERROR_NO_FILE'          : "Error while uploading: file not exists.",
        'UPLOAD_ERROR_UNSUPPORTED'      : "Error while uploading: unsupported format of the file.",
        'UPLOAD_ERROR_CORRUPTED_AUDIO'  : "Error while uploading: file appears to be corrupted.",
        'UPLOAD_ERROR_NO_SPACE'         : "Error while uploading: no time left on your account.",
        'UPLOAD_WAS_NOT_ADDED'          : "Error while uploading: database error.",
        'UPLOAD_SUCCESS'                : "Upload competed successfully.",
        'UPLOAD_ERROR_DISK_ACCESS_ERROR': "Error while uploading: internal server error."
    },
    'stream' : {
        'SET_ERROR_NO_STREAM'                       : "Can't change current playing track: stream not exists.",
        'SET_ERROR_NOT_STREAM_OWNER'                : "Can't change current playing track: user isn't stream owner.",
        'SET_ERROR_NO_TRACK'                        : "Can't change current playing track: track not exists.",
        'SET_SUCCESS'                               : "Current playing track changed successfully.",

        'CHANGE_STATE_ERROR_NO_STREAM'              : "Can't change stream state: stream not exists.",
        'CHANGE_STATE_ERROR_NOT_STREAM_OWNER'       : "Can't change stream state: user isn't stream owner.",
        'CHANGE_STATE_SUCCESS'                      : "Stream state changed successfully.",

        'REMOVE_FROM_STREAM_ERROR_NO_STREAM'        : "Can't remove track from stream: stream not exists.",
        'REMOVE_FROM_STREAM_ERROR_NOT_STREAM_OWNER' : "Can't remove track from stream: user isn't stream owner.",
        'REMOVE_FROM_STREAM_SUCCESS'                : "Track successfully removed from stream.",
        'REMOVE_FROM_STREAM_ERROR_NOT_IN_DB'        : "Can't remove track from stream: file not found in database.",
        
        'ADD_TO_STREAM_ERROR_NO_STREAM'             : "Add to stream error: stream not exists.",
        'ADD_TO_STREAM_ERROR_NOT_STREAM_OWNER'      : "Add to stream error: user isn't stream owner.",
        'ADD_TO_STREAM_SUCCESS'                     : "Track(s) successfully added to stream.",
        
        'REARRANGE_STREAM_ERROR_NO_STREAM'          : "Rearrange error: stream not exists.",
        'REARRANGE_STREAM_ERROR_NOT_STREAM_OWNER'   : "Rearrange error: user isn't stream owner.",
        'REARRANGE_STREAM_ERROR_COUNT_MISMATCH'     : "Rearrange error: tracks count mismatch.",
        'REARRANGE_STREAM_ERROR_REPEATS_FOUND'      : "Rearrange error: repeats found.",
        'REARRANGE_STREAM_ERROR_UNKNOWN_TRACK'      : "Rearrange error: track not",
        'REARRANGE_STREAM_SUCCESS'                  : "Stream rearranged successfully",
        'REARRANGE_NOT_CHANGED'                     : "Stream wasn't rearranged",
        
        'CHANGE_INFO_ERROR_STREAM_NOT_EXISTS'       : "Change stream info failed: stream not exists.",
        'CHANGE_INFO_ERROR_UNAUTHORIZED'            : "Change stream info failed: user isn't stream owner.",
        'CHANGE_INFO_SUCCESS'                       : "Stream info changed successfully.",
        'CHANGE_INFO_UNCHANGED'                     : "Stream info not changed.",
        
        'STREAM_DELETE_ERROR_NO_STREAM'             : "Can't delete stream: stream not exists.",
        'STREAM_DELETE_ERROR_UNAUTHORIZED'          : "Can't delete stream: user isn't stream owner.",
        'STREAM_DELETE_SUCCESS'                     : "Stream deleted successfully.",
        
        'CREATE_STREAM_ERROR_UNAUTHORIZED'          : "Can't create stream: user isn't owner.",
        'CREATE_STREAM_SUCCESS'                     : "New stream created successfully.",
        'CREATE_STREAM_ERROR'                       : "Can't create stream: database error.",
        'CREATE_STREAM_ERROR_PERM_USED'             : "Can't create stream: permalink already used my other stream"
    }
};

function langGetMessage(section, code)
{
    if(typeof langMessages[section] === "undefined")
    {
        return code;
    }
    if(typeof langMessages[section][code] === "undefined")
    {
        return code;
    }
    return langMessages[section][code];
}