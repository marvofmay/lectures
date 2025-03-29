# OpenAPI\Client\LECTURESApi

All URIs are relative to http://127.0.0.1:81, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**apiLecturesLectureUUIDStudentsStudentUUIDDelete()**](LECTURESApi.md#apiLecturesLectureUUIDStudentsStudentUUIDDelete) | **DELETE** /api/lectures/{lectureUUID}/students/{studentUUID} | Usuń studenta z wykładu |
| [**apiLecturesPost()**](LECTURESApi.md#apiLecturesPost) | **POST** /api/lectures | Dodaje wykład |
| [**apiLecturesUuidEnrollPost()**](LECTURESApi.md#apiLecturesUuidEnrollPost) | **POST** /api/lectures/{uuid}/enroll | Zapisywanie się na wykład |
| [**apiStudentsLecturesGet()**](LECTURESApi.md#apiStudentsLecturesGet) | **GET** /api/students/lectures | Pobiera listę wykładów |


## `apiLecturesLectureUUIDStudentsStudentUUIDDelete()`

```php
apiLecturesLectureUUIDStudentsStudentUUIDDelete($lecture_uuid, $student_uuid)
```

Usuń studenta z wykładu

Pozwala wykładowcy usnąć studenta z swojego wykładu.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\LECTURESApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$lecture_uuid = 'lecture_uuid_example'; // string | ID wykładu
$student_uuid = 'student_uuid_example'; // string | ID studenta

try {
    $apiInstance->apiLecturesLectureUUIDStudentsStudentUUIDDelete($lecture_uuid, $student_uuid);
} catch (Exception $e) {
    echo 'Exception when calling LECTURESApi->apiLecturesLectureUUIDStudentsStudentUUIDDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **lecture_uuid** | **string**| ID wykładu | |
| **student_uuid** | **string**| ID studenta | |

### Return type

void (empty response body)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiLecturesPost()`

```php
apiLecturesPost($api_lectures_post_request): \OpenAPI\Client\Model\ApiLecturesPost201Response
```

Dodaje wykład

Pozwala na dodanie wykładu przez wykładowcę.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\LECTURESApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$api_lectures_post_request = new \OpenAPI\Client\Model\ApiLecturesPostRequest(); // \OpenAPI\Client\Model\ApiLecturesPostRequest

try {
    $result = $apiInstance->apiLecturesPost($api_lectures_post_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling LECTURESApi->apiLecturesPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **api_lectures_post_request** | [**\OpenAPI\Client\Model\ApiLecturesPostRequest**](../Model/ApiLecturesPostRequest.md)|  | |

### Return type

[**\OpenAPI\Client\Model\ApiLecturesPost201Response**](../Model/ApiLecturesPost201Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiLecturesUuidEnrollPost()`

```php
apiLecturesUuidEnrollPost($lecture_uuid): \OpenAPI\Client\Model\ApiLecturesUuidEnrollPost201Response
```

Zapisywanie się na wykład

Pozwala studentowi zapisać się na wykład.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\LECTURESApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$lecture_uuid = 'lecture_uuid_example'; // string | ID wykładu na który student chce się zapisać

try {
    $result = $apiInstance->apiLecturesUuidEnrollPost($lecture_uuid);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling LECTURESApi->apiLecturesUuidEnrollPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **lecture_uuid** | **string**| ID wykładu na który student chce się zapisać | |

### Return type

[**\OpenAPI\Client\Model\ApiLecturesUuidEnrollPost201Response**](../Model/ApiLecturesUuidEnrollPost201Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiStudentsLecturesGet()`

```php
apiStudentsLecturesGet(): \OpenAPI\Client\Model\ApiStudentsLecturesGet200Response
```

Pobiera listę wykładów

Pozwala studentowi na pobranie listy wykładów na które się zapisał

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\LECTURESApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);

try {
    $result = $apiInstance->apiStudentsLecturesGet();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling LECTURESApi->apiStudentsLecturesGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

This endpoint does not need any parameter.

### Return type

[**\OpenAPI\Client\Model\ApiStudentsLecturesGet200Response**](../Model/ApiStudentsLecturesGet200Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
