<?php

namespace P2P\Amelia\Repository;

class ProviderRepository {

    /**
     * @param $slug
     * @return array
     */
    public function getWithServices($criteria) {
        global $wpdb;

        $where = [];
        $params = ['provider', 'visible'];        

        if ($criteria['location']) {
          $params[] = $criteria['location'];
          $where[] = "lt.locationId = %d";
        }

        if ($criteria['category']) {
          $params[] = $criteria['category'];
          $where[] = "s.categoryId = %d";
        }

        $where = $where ? ' AND ' . implode(' AND ', $where) : '';

        $sql = "SELECT
            u.id AS user_id,
            u.slug as user_slug,
            u.status AS user_status,
            u.externalId AS external_id,
            u.firstName AS user_firstName,
            u.lastName AS user_lastName,
            u.email AS user_email,
            u.note AS note,
            u.phone AS phone,
            u.pictureFullPath AS picture_full_path,
            u.pictureThumbPath AS picture_thumb_path,
            u.zoomUserId AS user_zoom_user_id,
            lt.locationId AS user_locationId,
            st.serviceId AS service_id,
            st.price AS service_price,
            st.minCapacity AS service_minCapacity,
            st.maxCapacity AS service_maxCapacity,
            s.name AS service_name,
            s.description AS service_description,
            s.color AS service_color,
            s.status AS service_status,
            s.categoryId AS service_categoryId,
            c.name AS service_categoryName,
            c.slug AS service_categorySlug,
            s.duration AS service_duration,
            s.bringingAnyone AS service_bringingAnyone,
            s.show AS service_show,
            s.aggregatedPrice AS service_aggregatedPrice,
            s.pictureFullPath AS service_picture_full,
            s.pictureThumbPath AS service_picture_thumb,
            s.recurringCycle AS service_recurringCycle,
            s.recurringSub AS service_recurringSub,
            s.recurringPayment AS service_recurringPayment,
            s.settings AS service_settings,
            s.translations AS service_translations,
            s.deposit AS service_deposit,
            s.depositPayment AS service_depositPayment,
            s.depositPerPerson AS service_depositPerPerson
        FROM {$wpdb->prefix}amelia_users u
        LEFT JOIN {$wpdb->prefix}amelia_providers_to_locations lt ON lt.userId = u.id
        LEFT JOIN {$wpdb->prefix}amelia_providers_to_services st ON st.userId = u.id
        LEFT JOIN {$wpdb->prefix}amelia_services s ON s.id = st.serviceId
        LEFT JOIN {$wpdb->prefix}amelia_categories c ON c.id = s.categoryId
        WHERE u.type = %s AND u.status = %s $where
        ORDER BY u.slug";
        $sql = $wpdb->prepare($sql, $params);

        $result = $wpdb->get_results($sql, ARRAY_A);

        $providerRows = [];
        $serviceRows = [];
        $providerServiceRows = [];

        if (!$result) return [];


        foreach ($result as $row) {
            $this->parseUserRow($row, $providerRows, $serviceRows, $providerServiceRows);
        }

        $providers = [];
        foreach ($providerRows as $providerKey => $providerArray) {
            $providers[$providerKey] = $providerArray;

            if ($providerServiceRows && array_key_exists($providerKey, $providerServiceRows)) {
                $providers[$providerKey]['services'] = [];
                foreach ((array)$providerServiceRows[$providerKey] as $serviceKey => $providerService) {
                    if (array_key_exists($serviceKey, $serviceRows)) {
                        $providers[$providerKey]['services'][$serviceKey] = array_merge(
                            $serviceRows[$serviceKey],
                            $providerService
                        ); 
                    }
                }
            }
        }

        return $providers;
    }

    private function parseUserRow($row, &$providerRows, &$serviceRows, &$providerServiceRows)
    {
        $userId = (int)$row['user_id'];
        $userSlug = isset($row['user_slug']) ? $row['user_slug'] : null;
        $serviceId = isset($row['service_id']) ? (int)$row['service_id'] : null;
        $extraId = isset($row['extra_id']) ? $row['extra_id'] : null;
        $couponId = isset($row['coupon_id']) ? $row['coupon_id'] : null;
        $googleCalendarId = isset($row['google_calendar_id']) ? $row['google_calendar_id'] : null;
        $outlookCalendarId = isset($row['outlook_calendar_id']) ? $row['outlook_calendar_id'] : null;
        $weekDayId = isset($row['weekDay_id']) ? $row['weekDay_id'] : null;
        $timeOutId = isset($row['timeOut_id']) ? $row['timeOut_id'] : null;
        $periodId = isset($row['period_id']) ? $row['period_id'] : null;
        $periodServiceId = isset($row['periodService_id']) ? $row['periodService_id'] : null;
        $periodLocationId = isset($row['periodLocation_id']) ? $row['periodLocation_id'] : null;
        $specialDayId = isset($row['specialDay_id']) ? $row['specialDay_id'] : null;
        $specialDayPeriodId = isset($row['specialDayPeriod_id']) ? $row['specialDayPeriod_id'] : null;
        $specialDayPeriodServiceId = isset($row['specialDayPeriodService_id'])
            ? $row['specialDayPeriodService_id'] : null;
        $specialDayPeriodLocationId = isset($row['specialDayPeriodLocation_id'])
            ? $row['specialDayPeriodLocation_id'] : null;
        $dayOffId = isset($row['dayOff_id']) ? $row['dayOff_id'] : null;

        if (!array_key_exists($userId, $providerRows)) {
            $providerRows[$userId] = [
                'id'               => $userId,
                'slug'             => $userSlug,                
                'type'             => 'provider',
                'status'           => isset($row['user_status']) ? $row['user_status'] : null,
                'externalId'       => isset($row['external_id']) ? $row['external_id'] : null,
                'firstName'        => $row['user_firstName'],
                'lastName'         => $row['user_lastName'],
                'email'            => $row['user_email'],
                'note'             => isset($row['note']) ? $row['note'] : null,
                'description'      => isset($row['description']) ? $row['description'] : null,
                'phone'            => isset($row['phone']) ? $row['phone'] : null,
                'zoomUserId'       => isset($row['user_zoom_user_id']) ? $row['user_zoom_user_id'] : null,
                'countryPhoneIso'  => isset($row['user_countryPhoneIso']) ? $row['user_countryPhoneIso'] : null,
                'locationId'       => isset($row['user_locationId']) ? $row['user_locationId'] : null,
                'pictureFullPath'  => isset($row['picture_full_path']) ? $row['picture_full_path'] : null,
                'pictureThumbPath' => isset($row['picture_thumb_path']) ? $row['picture_thumb_path'] : null,
                'translations'     => $row['user_translations'],
                'googleCalendar'   => [],
                'weekDayList'      => [],
                'dayOffList'       => [],
                'specialDayList'   => [],
                'serviceList'      => [],
                'timeZone'         => isset($row['user_timeZone']) ? $row['user_timeZone'] : null,
            ];
        }

        if ($googleCalendarId &&
            array_key_exists($userId, $providerRows) &&
            empty($providerRows[$userId]['googleCalendar'])
        ) {
            $providerRows[$userId]['googleCalendar']['id'] = $row['google_calendar_id'];
            $providerRows[$userId]['googleCalendar']['token'] = $row['google_calendar_token'];
            $providerRows[$userId]['googleCalendar']['calendarId'] = isset($row['google_calendar_calendar_id']) ? $row['google_calendar_calendar_id'] : null;
        }

        if ($outlookCalendarId &&
            array_key_exists($userId, $providerRows) &&
            empty($providerRows[$userId]['outlookCalendar'])
        ) {
            $providerRows[$userId]['outlookCalendar']['id'] = $row['outlook_calendar_id'];
            $providerRows[$userId]['outlookCalendar']['token'] = $row['outlook_calendar_token'];
            $providerRows[$userId]['outlookCalendar']['calendarId'] = isset($row['outlook_calendar_calendar_id']) ? $row['outlook_calendar_calendar_id'] : null;
        }

        if ($weekDayId &&
            array_key_exists($userId, $providerRows) &&
            !array_key_exists($weekDayId, $providerRows[$userId]['weekDayList'])
        ) {
            $providerRows[$userId]['weekDayList'][$weekDayId] = [
                'id'          => $weekDayId,
                'dayIndex'    => $row['weekDay_dayIndex'],
                'startTime'   => $row['weekDay_startTime'],
                'endTime'     => $row['weekDay_endTime'],
                'timeOutList' => [],
                'periodList'  => [],
            ];
        }

        if ($periodId &&
            $weekDayId &&
            array_key_exists($userId, $providerRows) &&
            array_key_exists($weekDayId, $providerRows[$userId]['weekDayList']) &&
            !array_key_exists($periodId, $providerRows[$userId]['weekDayList'][$weekDayId]['periodList'])
        ) {
            $providerRows[$userId]['weekDayList'][$weekDayId]['periodList'][$periodId] = [
                'id'                 => $periodId,
                'startTime'          => $row['period_startTime'],
                'endTime'            => $row['period_endTime'],
                'locationId'         => $row['period_locationId'],
                'periodServiceList'  => [],
                'periodLocationList' => [],
            ];
        }

        if ($periodServiceId &&
            $periodId &&
            $weekDayId &&
            array_key_exists($userId, $providerRows) &&
            array_key_exists($weekDayId, $providerRows[$userId]['weekDayList']) &&
            array_key_exists($periodId, $providerRows[$userId]['weekDayList'][$weekDayId]['periodList']) &&
            !array_key_exists($periodServiceId, $providerRows[$userId]['weekDayList'][$weekDayId]['periodList'][$periodId]['periodServiceList'])
        ) {
            $providerRows[$userId]['weekDayList'][$weekDayId]['periodList'][$periodId]['periodServiceList'][$periodServiceId] = [
                'id'        => $periodServiceId,
                'serviceId' => $row['periodService_serviceId'],
            ];
        }

        if ($periodLocationId &&
            $periodId &&
            $weekDayId &&
            array_key_exists($userId, $providerRows) &&
            array_key_exists($weekDayId, $providerRows[$userId]['weekDayList']) &&
            array_key_exists($periodId, $providerRows[$userId]['weekDayList'][$weekDayId]['periodList']) &&
            !array_key_exists($periodLocationId, $providerRows[$userId]['weekDayList'][$weekDayId]['periodList'][$periodId]['periodLocationList'])
        ) {
            $providerRows[$userId]['weekDayList'][$weekDayId]['periodList'][$periodId]['periodLocationList'][$periodLocationId] = [
                'id'         => $periodLocationId,
                'locationId' => $row['periodLocation_locationId'],
            ];
        }

        if ($timeOutId &&
            $weekDayId &&
            array_key_exists($userId, $providerRows) &&
            array_key_exists($weekDayId, $providerRows[$userId]['weekDayList']) &&
            !array_key_exists($timeOutId, $providerRows[$userId]['weekDayList'][$weekDayId]['timeOutList'])
        ) {
            $providerRows[$userId]['weekDayList'][$weekDayId]['timeOutList'][$timeOutId] = [
                'id'        => $timeOutId,
                'startTime' => $row['timeOut_startTime'],
                'endTime'   => $row['timeOut_endTime'],
            ];
        }

        if ($specialDayId &&
            array_key_exists($userId, $providerRows) &&
            !array_key_exists($specialDayId, $providerRows[$userId]['specialDayList'])
        ) {
            $providerRows[$userId]['specialDayList'][$specialDayId] = [
                'id'         => $specialDayId,
                'startDate'  => $row['specialDay_startDate'],
                'endDate'    => $row['specialDay_endDate'],
                'periodList' => [],
            ];
        }

        if ($specialDayPeriodId &&
            $specialDayId &&
            array_key_exists($userId, $providerRows) &&
            array_key_exists($specialDayId, $providerRows[$userId]['specialDayList']) &&
            !array_key_exists($specialDayPeriodId, $providerRows[$userId]['specialDayList'][$specialDayId]['periodList'])
        ) {
            $providerRows[$userId]['specialDayList'][$specialDayId]['periodList'][$specialDayPeriodId] = [
                'id'                 => $specialDayPeriodId,
                'startTime'          => $row['specialDayPeriod_startTime'],
                'endTime'            => $row['specialDayPeriod_endTime'],
                'locationId'         => $row['specialDayPeriod_locationId'],
                'periodServiceList'  => [],
                'periodLocationList' => [],
            ];
        }

        if ($specialDayPeriodServiceId &&
            $specialDayPeriodId &&
            $specialDayId &&
            array_key_exists($userId, $providerRows) &&
            array_key_exists($specialDayId, $providerRows[$userId]['specialDayList']) &&
            array_key_exists($specialDayPeriodId, $providerRows[$userId]['specialDayList'][$specialDayId]['periodList']) &&
            !array_key_exists($specialDayPeriodServiceId, $providerRows[$userId]['specialDayList'][$specialDayId]['periodList'][$specialDayPeriodId]['periodServiceList'])
        ) {
            $providerRows[$userId]['specialDayList'][$specialDayId]['periodList'][$specialDayPeriodId]['periodServiceList'][$specialDayPeriodServiceId] = [
                'id'        => $specialDayPeriodServiceId,
                'serviceId' => $row['specialDayPeriodService_serviceId'],
            ];
        }

        if ($specialDayPeriodLocationId &&
            $specialDayPeriodId &&
            $specialDayId &&
            array_key_exists($userId, $providerRows) &&
            array_key_exists($specialDayId, $providerRows[$userId]['specialDayList']) &&
            array_key_exists($specialDayPeriodId, $providerRows[$userId]['specialDayList'][$specialDayId]['periodList']) &&
            !array_key_exists($specialDayPeriodLocationId, $providerRows[$userId]['specialDayList'][$specialDayId]['periodList'][$specialDayPeriodId]['periodLocationList'])
        ) {
            $providerRows[$userId]['specialDayList'][$specialDayId]['periodList'][$specialDayPeriodId]['periodLocationList'][$specialDayPeriodLocationId] = [
                'id'         => $specialDayPeriodLocationId,
                'locationId' => $row['specialDayPeriodLocation_locationId'],
            ];
        }

        if ($dayOffId &&
            array_key_exists($userId, $providerRows) &&
            !array_key_exists($dayOffId, $providerRows[$userId]['dayOffList'])
        ) {
            $providerRows[$userId]['dayOffList'][$dayOffId] = [
                'id'        => $dayOffId,
                'name'      => $row['dayOff_name'],
                'startDate' => $row['dayOff_startDate'],
                'endDate'   => $row['dayOff_endDate'],
                'repeat'    => $row['dayOff_repeat'],
            ];
        }

        if ($serviceId &&
            !array_key_exists($serviceId, $serviceRows)
        ) {
            $serviceRows[$serviceId] = [
                'id'               => $serviceId,
                'customPricing'    => isset($row['service_customPricing']) ? $row['service_customPricing'] : null,
                'price'            => $row['service_price'],
                'minCapacity'      => $row['service_minCapacity'],
                'maxCapacity'      => $row['service_maxCapacity'],
                'name'             => isset($row['service_name']) ? $row['service_name'] : null,
                'description'      => isset($row['service_description']) ? $row['service_description'] : null,
                'color'            => isset($row['service_color']) ? $row['service_color'] : null,
                'status'           => isset($row['service_status']) ? $row['service_status'] : null,
                'categoryId'       => isset($row['service_categoryId']) ? (int)$row['service_categoryId'] : null,
                'categoryName'     => isset($row['service_categoryName']) ? $row['service_categoryName'] : null,
                'categorySlug'     => isset($row['service_categorySlug']) ? $row['service_categorySlug'] : null,                
                'duration'         => $row['service_duration'],
                'duration'         => isset($row['service_duration']) ? $row['service_duration'] : null,
                'bringingAnyone'   => isset($row['service_bringingAnyone']) ? $row['service_bringingAnyone'] : null,
                'show'             => isset($row['service_show']) ? $row['service_show'] : null,
                'aggregatedPrice'  => isset($row['service_aggregatedPrice']) ? $row['service_aggregatedPrice'] : null,
                'pictureFullPath'  => isset($row['service_picture_full']) ? $row['service_picture_full'] : null,
                'pictureThumbPath' => isset($row['service_picture_thumb']) ? $row['service_picture_thumb'] : null,
                'timeBefore'       => isset($row['service_timeBefore']) ? $row['service_timeBefore'] : null,
                'timeAfter'        => isset($row['service_timeAfter']) ? $row['service_timeAfter'] : null,
                'extras'           => [],
                'coupons'          => [],
                'settings'         => isset($row['service_settings']) ? $row['service_settings'] : null,
                'recurringCycle'   => isset($row['service_recurringCycle']) ? $row['service_recurringCycle'] : null,
                'recurringSub'     => isset($row['service_recurringSub']) ? $row['service_recurringSub'] : null,
                'recurringPayment' => isset($row['service_recurringPayment']) ? $row['service_recurringPayment'] : null,
                'translations'     => isset($row['service_translations']) ? $row['service_translations'] : null,
                'deposit'          => isset($row['service_deposit']) ? $row['service_deposit'] : null,
                'depositPayment'   => isset($row['service_depositPayment']) ? $row['service_depositPayment'] : null,
                'depositPerPerson' => isset($row['service_depositPerPerson']) ? $row['service_depositPerPerson'] : null,
            ];
        }

        if ($extraId &&
            $serviceId &&
            array_key_exists($serviceId, $serviceRows) &&
            !array_key_exists($extraId, $serviceRows[$serviceId]['extras'])
        ) {
            $serviceRows[$serviceId]['extras'][$extraId] = [
                'id'              => $extraId,
                'name'            => $row['extra_name'],
                'price'           => $row['extra_price'],
                'maxQuantity'     => $row['extra_maxQuantity'],
                'position'        => $row['extra_position'],
                'aggregatedPrice' => $row['extra_aggregatedPrice'],
                'description'     => $row['extra_description']
            ];
        }

        if ($couponId &&
            $serviceId &&
            array_key_exists($serviceId, $serviceRows) &&
            !array_key_exists($couponId, $serviceRows[$serviceId]['coupons'])
        ) {
            $serviceRows[$serviceId]['coupons'][$couponId] = [
                'id'            => $couponId,
                'code'          => $row['coupon_code'],
                'discount'      => $row['coupon_discount'],
                'deduction'     => $row['coupon_deduction'],
                'limit'         => $row['coupon_limit'],
                'customerLimit' => $row['coupon_customerLimit'],
                'status'        => $row['coupon_status']
            ];
        }

        if ($serviceId && (!array_key_exists($userId, $providerServiceRows) || !array_key_exists($serviceId, $providerServiceRows[$userId]))) {
            $providerServiceRows[$userId][$serviceId] = [
                'price'         => $row['service_price'],
                'customPricing' => $row['service_customPricing'],
                'minCapacity'   => (int)$row['service_minCapacity'],
                'maxCapacity'   => (int)$row['service_maxCapacity']
            ];
        }
    }

}
