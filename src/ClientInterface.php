<?php
/**
 * Copyright (c) 2016 AlexaCRM.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Lesser Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * AlexaCRM\CRMToolkit\AlexaSDK_Abstract.php
 *
 * @author alexacrm.com
 * @version 1.0
 * @package AlexaCRM\CRMToolkit\AlexaSDK
 */

namespace AlexaCRM\CRMToolkit;

/**
 * This interface that contains constants for new entity record creation and max record to retrieve
 */
interface ClientInterface {

    /**
     * Default GUID for "not known" or new Entities
     *
     * @var String Parameter based on Dynamics CRM guid format
     */
    const EmptyGUID = '00000000-0000-0000-0000-000000000000';

    /**
     * Maximum number of records in a single RetrieveMultiple
     *
     * @var Integer
     */
    const MAX_CRM_RECORDS = 5000;

}
